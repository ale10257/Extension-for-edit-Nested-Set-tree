$(function () {
    var div = $('#tree-table');
    div.on('click', '.fa-folder-open, .fa-folder', function () {
        var attrClass, action;
        var level = $(this).parents('tr').data('level');
        if ($(this).hasClass('fa-folder-open')) {
            attrClass = 'fa fa-folder parent';
            action = 'hide';
        } else {
            attrClass = 'fa fa-folder-open parent';
            action = 'show';
        }
        $(this).attr('class', attrClass);
        $(this).parents('tr').nextAll('tr').each(function () {
            if ($(this).hasClass('hide-show') && level < $(this).data('level')) {
                var parent = $(this).find('.parent');
                if (parent.length === 1) {
                    parent.attr('class', attrClass);
                }
                if (action === 'hide') {
                    $(this).hide()
                } else {
                    $(this).show()
                }
            } else {
                return false;
            }
        });
    });
    function init() {
        $('.sortable tbody').sortable({
            containment: "parent",
            cursor: "move",
            stop: stopHandler,
            delay : 200
        });
    }
    init();
    function newTree(obj) {
        $.post(div.data('url'), obj, function (data) {
            div.html(data);
            init();
        });
        init();
    }
    function stopHandler(event, ui) {
        var level = ui.item.data('level');
        var next = ui.item.next('tr');
        var prev = ui.item.prev('tr');
        var obj = {
            'first': ui.item.data('id')
        };
        if (level === next.data('level')) {
            obj['two'] = next.data('id');
            obj['action'] = 'before';
            newTree(obj);
            return true;
        }
        if (level === prev.data('level')) {
            obj['two'] = prev.data('id');
            obj['action'] = 'after';
            newTree(obj);
            return true;
        }
        return false;
    }
    $('.delete-item-tree').click(function (e) {
        e.preventDefault();
        if (confirm('Вы уверены?')) {
            $.post(this.href, function () {
                location.reload();
            });
        }
    });
});
