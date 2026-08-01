export default function listPlugin(editor) {
    editor.DomComponents.addType('unordered-list', {
        isComponent: el => {
            if (el.tagName === 'UL') {
                return {
                    type: 'unordered-list'
                };
            }
            return false;
        },
        model: {
            defaults: {
                tagName: 'ul',
                droppable: 'li',
                toolbar: [
                    {
                        attributes: {
                            class: 'fa fa-plus',
                            title: 'Add list item'
                        },
                        command: 'add-list-item'
                    },
                    {
                        attributes: {
                            class: 'fa fa-clone',
                            title: 'Duplicate list'
                        },
                        command: 'tlb-clone'
                    },
                    {
                        attributes: {
                            class: 'fa fa-trash'
                        },
                        command: 'tlb-delete'
                    }
                ],
                components: [
                    {
                        type: 'text',
                        tagName: 'li',
                        content: 'List item 1'
                    },
                    {
                        type: 'text',
                        tagName: 'li',
                        content: 'List item 2'
                    }
                ]
            }
        }
    });

    editor.Commands.add('add-list-item', {
        run(editor) {
            const selected = editor.getSelected();

            if (!selected || selected.get('type') !== 'unordered-list') {
                return;
            }

            const newItem = selected.components().add({
                type: 'text',
                tagName: 'li',
                content: 'New list item'
            });

            editor.select(newItem);
            editor.runCommand('core:component-edit');
        }
    });

    editor.BlockManager.add('unordered-list', {
        label: 'Unordered List',
        category: 'Basic',
        attributes: {
            class: 'fa fa-list-ul',
            title: 'Unordered List'
        },
        content: {
            type: 'unordered-list'
        }
    });
}