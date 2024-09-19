(function ($) {
    'use strict';

    $('document').ready(function () {
        geot.rules.init();
    });

    var geot = {rules: null}

    /*
    *  Rules
    *
    *  Js for needed for rules
    *
    *  @since: 1.0.0
    *  Thanks to advanced custom fields plugin for part of this code
    */

    geot.rules = {
        $el: null,
        $main_table: null,
        init: function () {
            // vars

            var _this = this;
            $('select.selectize').each( function () {
                _this.init_selectize($(this));
            });

            // $el
            _this.$el = $('.geot-rules-table');
            _this.$main_table = $('.geot-rules-main-table');

            // add rule
            _this.$el.on('click', '.rules-add-rule', function () {

                _this.add_rule($(this).closest('tr'));
                return false;
            });


            // remove rule
            _this.$el.on('click', '.rules-remove-rule', function () {

                _this.remove_rule($(this).closest('tr'));
                return false;
            });


            // add rule
            _this.$el.on('click', '.rules-add-group', function () {

                _this.add_group();
                return false;
            });


            // change rule
            _this.$el.on('change', '.param select', function () {

                // vars
                var $tr = $(this).closest('tr'),
                    rule_id = $tr.attr('data-id'),
                    $group = $tr.closest('.rules-group'),
                    group_id = $group.attr('data-id'),
                    val_td = $tr.find('td.value'),
                    param = $(this).val(),
                    ajax_data = {
                        'action': "geot/field_group/render_rules",
                        'nonce': geot_js.nonce,
                        'rule_id': rule_id,
                        'group_id': group_id,
                        'value': '',
                        'param': param,
                        'name': $(this).attr('name')
                    };


                // add loading gif
                var div = $('<div class="geot-loading"><img src="' + geot_js.admin_url + '/images/wpspin_light.gif"/> </div>');
                val_td.html(div);

                // load rules html
                $.ajax({
                    url: ajaxurl,
                    data: ajax_data,
                    type: 'post',
                    dataType: 'html',
                    success: function (html) {
                        val_td.html(html);
                        $('.value #geot_rule_'+group_id+'_rule_'+ rule_id+'.selectize').selectize({
                                        valueField: 'id',
                                        labelField: 'name',
                                        searchField: 'name',
                                        loadThrottle: 500,
                                        options: [],
                                        render: {
                                            option: function (item, escape) {
                                                return '<div>' + escape(item.name) + '</div>';
                                            }
                                        },
                                        load: function (query, callback) {
                                            self = this;
                                            if (!query.length) return callback();
                                            $.ajax({
                                                url: ajaxurl,
                                                data: {'action': "geot/field_group/render_options",
                                                    'nonce': geot_js.nonce, param,
                                                    'q': query
                                                    },
                                                type: 'POST',
                                                dataType: 'json',
                                                error: function () {
                                                    callback();
                                                },
                                                success: function (res) {
                                                    callback(res.data);
                                                }
                                            });
                                        }
                                    });
                    }
                });

                // Operators Rules
                var operator_td = $tr.find('td.operator'),
                    ajax_data = {
                        'action': "geot/field_group/render_operator",
                        'nonce': geot_js.nonce,
                        'rule_id': rule_id,
                        'group_id': group_id,
                        'value': '',
                        'param': $(this).val(),
                        'name': $(this).attr('name')
                    };

                operator_td.html(div);
                $.ajax({
                    url: ajaxurl,
                    data: ajax_data,
                    type: 'post',
                    dataType: 'html',
                    success: function (html) {

                        operator_td.html(html);
                    }
                });

            });
        },
        add_rule: function ($tr) {

            // vars
            var $tr2 = $tr.clone(),
                old_id = $tr.parent().find('tr').last().attr('data-id'),
                current_id = $tr2.attr('data-id'),
                new_id = 'rule_' + (parseInt(old_id.replace('rule_', ''), 10) + 1);

            // update names
            $tr2.find('[name]').each(function () {

                $(this).attr('name', $(this).attr('name').replace(current_id, new_id));
                $(this).attr('id', $(this).attr('id').replace(current_id, new_id));
            });

            // update data-i
            $tr2.attr('data-id', new_id);
            $tr2.find('.selectize-control').remove();

            // add tr
            $tr.after($tr2);
            this.init_selectize($tr2.find('select.selectize'));

            return false;
        },
        remove_rule: function ($tr) {

            // vars
            var siblings = $tr.siblings('tr').length;

            if (siblings == 0) {
                // remove group
                this.remove_group($tr.closest('.rules-group'));
            } else {
                // remove tr
                $tr.remove();
            }

        },
        add_group: function () {

            // vars
            var $group = this.$main_table.find('.rules-group:last'),
                $group2 = $group.clone(),
                old_id = $group2.attr('data-id'),
                new_id = 'group_' + (parseInt(old_id.replace('group_', ''), 10) + 1);

            // update names
            $group2.find('[name]').each(function () {

                $(this).attr('name', $(this).attr('name').replace(old_id, new_id));
                $(this).attr('id', $(this).attr('id').replace(old_id, new_id));
            });


            // update data-i
            $group2.attr('data-id', new_id);

            // update h4
            $group2.find('h4').html(geot_js.l10n.or).addClass('rules-or');

            // remove all tr's except the first one
            $group2.find('tr:not(:first)').remove();
            $group2.find('.selectize-control').remove();

            // add tr
            $group.after($group2);
            this.init_selectize($group2.find('select.selectize'));
        },
        remove_group: function ($group) {
            $group.remove();
        },
        init_selectize: function ( ele ) {

            const param = ele.data('param');
            ele.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                loadThrottle: 500,
                options: [],
                render: {
                    option: function (item, escape) {
                        return '<div>' + escape(item.name) + '</div>';
                    }
                },
                load: function (query, callback) {
                    self = this;
                    if (!query.length) return callback();
                    $.ajax({
                        url: ajaxurl,
                        data: {
                            'action': "geot/field_group/render_options",
                            'nonce': geot_js.nonce, param ,
                            'q': query
                        },
                        type: 'POST',
                        dataType: 'json',
                        error: function () {
                            callback();
                        },
                        success: function (res) {
                            callback(res.data);
                        }
                    });
                }
            });
        }
    };
})(jQuery);
