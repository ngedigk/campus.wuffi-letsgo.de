export default function headlinePlugin(editor) {
    editor.DomComponents.addType('headline', {

        extend: 'text',

        isComponent: el => {
            if (/^H[1-6]$/.test(el.tagName)) {
                return {
                    type: 'headline',
                    headlineLevel: el.tagName.toLowerCase()
                };
            }
            return false;
        },
        model: {
            defaults: {
                tagName: 'h4',
                content: 'Headline',
                headlineLevel: 'h4',
                traits: [
                    {
                        type: 'select',
                        name: 'tagName',
                        label: 'Headline level',
                        changeProp: true,
                        options: [
                            { id: 'h4', name: 'H4' },
                            { id: 'h5', name: 'H5' },
                            { id: 'h6', name: 'H6' }
                        ]
                    }
                ]
            },
            init() {
                this.on('change:headlineLevel', model => {
                    const level = model.get('headlineLevel');
                    if (!/^h[1-6]$/.test(level)) {
                        return;
                    }
                    this.set('tagName', level);
                });
            }
        }
    });

    editor.BlockManager.add('headline', {
        label: 'Headline',
        category: 'Basic',
        attributes: {
            class: 'fa fa-header',
            title: 'Headline'
        },
        content: {
            type: 'headline',
            tagName: 'h4',
            content: 'Headline'
        }
    });
}