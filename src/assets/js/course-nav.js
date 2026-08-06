(function () {
    'use strict';

    var COURSE_MAIN_SELECTOR = '#course-content';
    var NAV_SELECTOR = '[data-course-nav]';

    function extractFragment(html) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(html, 'text/html');
        var mainEl = doc.querySelector(COURSE_MAIN_SELECTOR);

        return mainEl ? mainEl.innerHTML : html;
    }
    
    function replaceFragment(html) {
        var mainEl = document.querySelector(COURSE_MAIN_SELECTOR);
        
        if (!mainEl) {
            return;
        }
        
        mainEl.innerHTML = extractFragment(html);
        mainEl.removeAttribute('data-loading');
    }

    function scrollToTop(scrollTarget) {
        if (!scrollTarget) {
            return
        };

        var target = document.querySelector(scrollTarget);

        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function loadFragment(url, options) {
        options = options || {};

        var mainEl = document.querySelector(COURSE_MAIN_SELECTOR);

        if (mainEl) {
            mainEl.setAttribute('data-loading', 'true');
        }
        
        var method = (options.method || 'GET').toUpperCase();

        var fetchOptions = {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        };
        
        if (options.body) {
            fetchOptions.body = options.body;
        }

        return fetch(url, fetchOptions)
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }

                return response.text();
            })
            .then(function (html) {
                replaceFragment(html);

                if (options.updateHistory !== false) {
                    history.pushState({ courseNav: true }, '', url);
                }
                
                scrollToTop(options.scrollTarget);
            })
            .catch(function (err) {
                console.error('Course navigation error:', err);

                if (mainEl) {
                    mainEl.removeAttribute('data-loading');
                }
                
                if (method === 'GET' && options.fallbackUrl) {
                    window.location.href = options.fallbackUrl;
                }
                
                throw error;
            });
    }

    function handleNavClick(event) {
        var link = event.target.closest('a' + NAV_SELECTOR);
        if (!link) return;

        var href = link.getAttribute('href');
        if (!href || href === '#' || href === 'javascript:void(0)') {
            return
        };
        
        var linkUrl;
        try {
            linkUrl = new URL(href, window.location.origin);
        } catch (error) {
            return;
        }

        if (linkUrl.origin !== window.location.origin) {
            return;
        }

        event.preventDefault();

        loadFragment(linkUrl.href, {
            method: 'GET',
            fallbackUrl: linkUrl.href,
            scrollTarget: link.getAttribute('data-scroll-target')
        }).catch(function () {
            /**
             * loadFragment already handled the GET fallback.
             * */ 
        });
    }

    function handleFormSubmit(event) {
        var form = event.target.closest('form' + NAV_SELECTOR);

        if (!form) {
            return;
        }
        
        event.preventDefault();
        
        var action = form.getAttribute('action') || window.location.href;
        var method = (form.getAttribute('method') || 'GET').toUpperCase();
        var formUrl;
        
        try {
            formUrl = new URL(action, window.location.origin);
        } catch (error) {
            form.submit();
            return;
        }
        
        if (formUrl.origin !== window.location.origin) {
            form.submit();
            return;
        }
        
        var formData = new FormData(form);

        if (method === 'GET') {
            formData.forEach(function (value, key) {
                formUrl.searchParams.append(key, value);
            });
        }
        
        loadFragment(formUrl.href, {
            method: method,
            body: method === 'GET' ? null : formData,
            updateHistory: false,
            scrollTarget: form.getAttribute('data-scroll-target')
        }).catch(function () {
            if (method === 'POST') {
                HTMLFormElement.prototype.submit.call(form);
            }
        });
    }

    function handlePopState(event) {
        if (!event.state || !event.state.courseNav) {
            return;
        }
        
        loadFragment(window.location.href, {
            method: 'GET',
            updateHistory: false
        }).catch(function () {
            /*
             * The GET fallback is handled by loadFragment().
             */
        });;
    }
    
    document.addEventListener('click', handleNavClick);
    document.addEventListener('submit', handleFormSubmit);
    window.addEventListener('popstate', handlePopState);
})();
