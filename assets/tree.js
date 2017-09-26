$(function () {
    var div = $('#tree-table');
    div.on('click', '.plus-minus', function () {
        var attrClass, action;
        var level = $(this).parents('tr').data('level');
        if ($(this).hasClass('fa-minus')) {
            attrClass = 'fa fa-plus plus-minus';
            action = 'hide';
        } else {
            attrClass = 'fa fa-minus plus-minus';
            action = 'show';
        }
        $(this).attr('class', attrClass);
        $(this).parents('tr').nextAll('tr').each(function () {
            if ($(this).hasClass('hide-show') && level < $(this).data('level')) {
                var td = $(this).find('.plus-minus');
                if (td.length === 1) {
                    td.attr('class', attrClass);
                }
                if (action === 'hide') {
                    $(this).hide(500)
                } else {
                    $(this).show(500)
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
            stop: stopHandler
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
