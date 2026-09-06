window.abpet_parent = window.abpet_parent || jQuery('div.abpet_admin');
let abpet_feature = JSON.parse(abpet_admin_data.abpet_feature);
let abpet_category = JSON.parse(abpet_admin_data.abpet_category);
let abpet_organizer = JSON.parse(abpet_admin_data.abpet_organizer);
let abpet_brand = JSON.parse(abpet_admin_data.abpet_brand);
let abpet_location = JSON.parse(abpet_admin_data.abpet_location);
let abpet_related_info = JSON.parse(abpet_admin_data.related_info);
let abpet_sp_info = JSON.parse(abpet_admin_data.sp_data);
function abpet_admin_init(target) {
    abpet_sortable(target);
    abpet_color_picker_init(target);
    //abpet_wp_editor_init(target);
    abpet_ticket_type_selection(target);
}
function abpet_sortable(target = abpet_parent) {
    let $sortable = target.find('.sortable_area:not(.abp_hidden *)');
    if ($sortable.length === 0) {
        $sortable = target.closest('.sortable_area');
    }
    if ($sortable.length > 0) {
        $sortable.sortable({
            handle: '.sortable_handle',
            stop: function (event, ui) {
                ui.item.trigger('abp_trigger');
            }
        });
    }
}
function abpet_color_picker_init(target = abpet_parent) {
    let $pickers = target.find('.abp_color_picker:not(.abp_hidden *)');
    if ($pickers.length > 0) {
        $pickers.wpColorPicker({
            change: function (event, ui) {
                setTimeout(function () {
                    jQuery(event.target).trigger('abp_trigger');
                }, 50);
            },
            clear: function (event) {
                setTimeout(function () {
                    jQuery(event.target).trigger('abp_trigger');
                }, 50);
            }
        });
    }
}
function abpet_wp_editor_init(target = abpet_parent) {
    let textArea = target.find('textarea.wp-editor-area');
    if (textArea.length > 0) {
        textArea.each(function () {
            let $currentTextarea = jQuery(this);
            let dynamicId = 'editor_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            $currentTextarea.attr('id', dynamicId);
            let $wrap = $currentTextarea.closest('.wp-editor-wrap');
            if ($wrap.length > 0) {
                $wrap.replaceWith($currentTextarea);
            }
            $currentTextarea.show();
            if (typeof wp !== 'undefined' && wp.editor) {
                wp.editor.remove(dynamicId);
                wp.editor.initialize(dynamicId, {
                    tinymce: {
                        wpautop: true,
                        cleanup: false,
                        verify_html: false,
                        entity_encoding: 'raw',
                        forced_root_block: false,
                        valid_elements: '*[*]',
                        setup: function (editor) {
                            editor.on('change keyup', function () {
                                editor.save();
                            });
                        }
                    },
                    quicktags: true,
                    mediaButtons: true
                });
            }
        });
    }
}
function abpet_ticket_type_selection(target = abpet_parent) {
    const $selects = target.find('.ticket_configuration [name="ticket_name[]"]');
    if ($selects.length > 0) {
        const selectedValues = $selects.map(function () {
            return jQuery(this).val();
        }).get().filter(value => value !== "");
        $selects.each(function () {
            const $currentSelect = jQuery(this);
            const currentValue = $currentSelect.val();
            $currentSelect.html('<option value="" selected>' + abpet_admin_data.msg.select_ticket + '</option>');
            abpet_ticket_type.forEach(function (ticket) {
                if (!selectedValues.includes(ticket.id.toString()) || ticket.id.toString() === currentValue) {
                    const $option = jQuery('<option></option>').val(ticket.id).text(ticket.label);
                    if (ticket.id.toString() === currentValue) {
                        $option.prop('selected', true);
                    }
                    $currentSelect.append($option);
                }
            });
        });
    }
}
function abpet_emoji_check(str) {
    return !(/^fa[bsrld]\s/.test(str));
}
//========== Global Function =================//
window.abpet_popup_open_global = function (action, id = '') {
    if (action) {
        jQuery('body').addClass('_stop_scroll').find('[data-popup="#abpet_global_popup"]').addClass('in').promise().done(function () {
            let parent = abpet_parent.find('[data-popup="#abpet_global_popup"]').find('.popup_area').addClass(action);
            id = id !== '' ? id : '';
            let target = parent.find('.popup_body');
            let post_id = abpet_parent.find("[name='abpet_post_id']").val() || '';
            jQuery.ajax({
                type: 'POST', url: abpet_admin_data.ajax_url, data: {
                    "action": 'abpet_add_' + action, 'id': id, 'post_id': post_id, 'nonce': abpet_admin_data.nonce
                }, beforeSend: function () {
                    abpet_spinner(parent);
                    setTimeout(function () {
                        abpet_color_picker_init(parent);
                    }, 50);
                    abpet_toast_msg(abpet_admin_data.msg.loading);
                }, success: function (response) {
                    abpet_spinner_remove(parent);
                    if (response.data && response.data.hasOwnProperty('html')) {
                        target.html(response.data.html).promise().done(function () {
                            abpet_toast_msg(response.data.msg, response.data.type);
                            abpet_init(target);
                        });
                    }
                }, error: function (xhr) {
                    abpet_ajx_error(xhr, parent);
                }
            })
        });
    }
};
window.abpet_popup_close_global = function () {
    let deferred = jQuery.Deferred();
    let target = abpet_parent.find('[data-popup="#abpet_global_popup"]');
    if (target.length > 0) {
        target.removeClass('in').promise().done(function () {
            jQuery('body').removeClass('_stop_scroll');
            target.find('.popup_area').removeClass().addClass('popup_area').find('.popup_body').html('').promise().done(function () {
                deferred.resolve(true);
            });
        });
    } else {
        deferred.resolve(true);
    }
    return deferred.promise();
};
window.abpet_save_global = function (action, $_this) {
    if (action) {
        let $this = jQuery($_this);
        let parent = $this.closest('.abp_form');
        let formData = abpet_get_form_data(parent);
        let post_page = abpet_parent.find("[name='abpet_post_id']");
        if (post_page.length > 0) {
            formData.append('post_id', post_page.val());
        }
        formData.append('nonce', abpet_admin_data.nonce);
        formData.append('action', 'abpet_save_' + action);
        jQuery.when(abpet_popup_close_global()).done(function (isClosed) {
            if (isClosed) {
                let target = abpet_parent.find('.' + action);
                jQuery.ajax({
                    type: 'POST',
                    url: abpet_admin_data.ajax_url,
                    contentType: false,
                    processData: false,
                    data: formData,
                    beforeSend: function () {
                        abpet_spinner(target);
                        abpet_toast_msg(abpet_admin_data.msg.saving);
                    },
                    success: function (response) {
                        if (target && target.length > 0 && response.data && response.data.hasOwnProperty('html')) {
                            target.html(response.data.html).promise().done(function () {
                                abpet_init(target);
                                abpet_wp_editor_init(target);
                            });
                        }
                        if (response.data.hasOwnProperty('js')) {
                            if (action === 'ticket_type') {
                                abpet_ticket_type = response.data.js;
                                abpet_sp_init();
                                abpet_ticket_type_selection();
                            }
                            if (action === 'decor_item') {
                                abpet_decor_item = response.data.js;
                                abpet_sp_init();
                            }
                            if (action === 'option_feature') {
                                abpet_feature = response.data.js;
                                new ABPET_Multi_Selection('div.abpet_admin .post_feature', abpet_feature);
                            }
                            if (action === 'tax_category') {
                                abpet_category = response.data.js;
                                new ABPET_Multi_Selection('div.abpet_admin .abpet_category', abpet_category);
                            }
                            if (action === 'tax_organizer') {
                                abpet_organizer = response.data.js;
                                new ABPET_Multi_Selection('div.abpet_admin .abpet_organizer', abpet_organizer);
                            }
                            if (action === 'tax_brand') {
                                abpet_brand = response.data.js;
                                new ABPET_Multi_Selection('div.abpet_admin .abpet_brand', abpet_brand);
                            }
                            if (action === 'tax_location') {
                                abpet_location = response.data.js;
                                new ABPET_Multi_Selection('div.abpet_admin .abpet_location', abpet_location);
                            }
                        }
                        abpet_spinner_remove(target);
                        abpet_toast_msg(response.data.msg, response.data.type);
                    },
                    error: function (xhr) {
                        abpet_ajx_error(xhr, target);
                    }
                });
            }
        });
    }
};
window.abpet_delete_global = function (action, id = '') {
    if (confirm(abpet_admin_data.msg.confirm_delete + ' \n\n' + abpet_admin_data.msg.confirm_ok + ' \n ' + abpet_admin_data.msg.confirm_cancel)) {
        if (action && id) {
            let target = abpet_parent.find('.' + action);
            jQuery.ajax({
                type: 'POST', url: abpet_admin_data.ajax_url, data: {
                    "action": 'abpet_delete_' + action, 'id': id, 'nonce': abpet_admin_data.nonce
                }, beforeSend: function () {
                    abpet_spinner(target);
                    abpet_toast_msg(abpet_admin_data.msg.deleting, 'error');
                }, success: function (response) {
                    if (response.data && response.data.hasOwnProperty('html')) {
                        target.html(response.data.html).promise().done(function () {
                            abpet_init(target);
                        });
                    }
                    if (response.data.hasOwnProperty('js')) {
                        if (action === 'ticket_type') {
                            abpet_ticket_type = response.data.js;
                            abpet_sp_init();
                        }
                        if (action === 'decor_item') {
                            abpet_decor_item = response.data.js;
                            abpet_sp_init();
                        }
                    }
                    abpet_toast_msg(response.data.msg, response.data.type);
                    abpet_spinner_remove(target);
                }, error: function (xhr) {
                    abpet_ajx_error(xhr, target);
                }
            })
        }
    }
};
window.abpet_post_action = function (action, id) {
    id = id !== '' ? parseInt(id) : '';
    if (action && !isNaN(id) && id !== '') {
        let parent = abpet_parent.find('.abpet_posts')
        jQuery.ajax({
            type: 'POST', url: abpet_admin_data.ajax_url, data: {
                "action": 'abpet_post_' + action, 'post_id': id, 'nonce': abpet_admin_data.nonce
            }, beforeSend: function () {
                abpet_spinner(parent);
                abpet_toast_msg((abpet_admin_data.msg[action] ? abpet_admin_data.msg[action] : abpet_admin_data.msg.loading), 'warn');
            }, success: function (response) {
                abpet_spinner_remove(parent);
                abpet_toast_msg(response.data.msg, response.data.type);
                window.location.reload();
            }, error: function (xhr) {
                abpet_ajx_error(xhr, parent);
            }
        });
    }
};
window.abpet_import_global = function (action) {
    if (action) {
        if (action === 'remove_dummy' && !window.confirm('Remove all ABP Event Ticket dummy data? Real events will not be removed.')) {
            return;
        }
        let target = abpet_parent.find('.' + action);
        if (action === 'dummy' || action === 'remove_dummy') {
            target = abpet_parent.find('.abp_status');
        }
        jQuery.ajax({
            type: 'POST', url: abpet_admin_data.ajax_url, data: {
                "action": 'abpet_import_' + action, 'nonce': abpet_admin_data.nonce
            }, beforeSend: function () {
                abpet_spinner(target);
                abpet_toast_msg((abpet_admin_data.msg[action] ? abpet_admin_data.msg[action] : abpet_admin_data.msg.loading));
            }, success: function (response) {
                abpet_spinner_remove(target);
                abpet_toast_msg(response.data.msg, response.data.type);
                if (action === 'dummy' || action === 'remove_dummy') {
                    window.location.reload();
                } else {
                    if (target && target.length > 0 && response.data && response.data.hasOwnProperty('html')) {
                        target.html(response.data.html).promise().done(function () {
                            abpet_init(target);
                            setTimeout(function () {
                                abpet_color_picker_init(target);
                                abpet_wp_editor_init(target);
                            }, 50);
                        });
                    }
                }
            }, error: function (xhr) {
                abpet_ajx_error(xhr, target);
            }
        });
    }
};
window.abpet_create_page = function (page_type) {
    if (page_type) {
        let parent = abpet_parent.find('.abp_status');
        jQuery.ajax({
            type: 'POST', url: abpet_admin_data.ajax_url, data: {
                "action": "abpet_create_page", 'nonce': abpet_admin_data.nonce, 'type': page_type
            }, beforeSend: function () {
                abpet_spinner(parent);
                abpet_toast_msg(abpet_admin_data.msg.create_post_page);
            }, success: function (response) {
                abpet_toast_msg(response.data.msg, response.data.type);
                window.location.reload();
            }, error: function (xhr) {
                abpet_ajx_error(xhr, target);
            }
        });
    }
};
window.abpet_wc_config = function (page_type) {
    if (page_type) {
        let parent = abpet_parent.find('.abp_status');
        jQuery.ajax({
            type: 'POST', url: abpet_admin_data.ajax_url, data: {
                "action": "abpet_wc_config", 'nonce': abpet_admin_data.nonce, 'type': page_type
            }, beforeSend: function () {
                abpet_spinner(parent);
                abpet_toast_msg((abpet_admin_data.msg[page_type] ? abpet_admin_data.msg[page_type] : abpet_admin_data.msg.loading));
            }, success: function (response) {
                if (response.data && response.data.hasOwnProperty('msg')) {
                    abpet_toast_msg(response.data.msg, response.data.type);
                }
                window.location.reload();
            }, error: function (xhr) {
                abpet_ajx_error(xhr, parent);
            }
        });
    }
};
//==================image selection========================//
let abpet_media_uploader;
window.abpet_image_remove = function ($this) {
    $this = jQuery($this);
    let parent = $this.closest('.image_selection');
    if (parent && parent.length > 0) {
        parent.find('input').val('');
        parent.find('img').attr('src', '');
        parent.find('button').slideDown('fast');
        parent.find('.image_item').slideUp('fast');
        let id = parent.find('input').attr('data-target');
        let target = jQuery(id);
        if (target && target.length > 0) {
            jQuery(target).css('background-image', '');
        }
    }
};
window.abpet_image_selection = function ($this) {
    $this = jQuery($this);
    if (!abpet_media_uploader) {
        abpet_media_uploader = wp.media({
            multiple: false
        });
        abpet_media_uploader.on('select', function () {
            let attachment = abpet_media_uploader.state().get('selection').first().toJSON();
            let parent = abpet_media_uploader.current_target;
            if (parent && parent.length > 0) {
                parent.find('input').val(attachment.id);
                parent.find('img').attr('src', attachment.url);
                parent.find('button').slideUp('fast');
                parent.find('.image_item').slideDown('fast');
                let id = parent.find('input').attr('data-target');
                let target = jQuery(id);
                if (target && target.length > 0) {
                    jQuery(target).css('background-image', `url(${attachment.url})`);
                }
            }
        });
    }
    abpet_media_uploader.current_target = $this.closest('.image_selection');
    abpet_media_uploader.open();
};
//==========Post Pagination=================//
abpet_parent.on('click', '.post_list .pagination_area button[data-page]', function () {
    let $this = jQuery(this);
    if (!$this.hasClass('abp_active')) {
        let parent = $this.closest('.abpet_posts');
        let target = parent.find('.post_list');
        let filter_args = {};
        if (parent.find("[name='select_hidden_post_status']").length > 0) {
            filter_args['status'] = parent.find("[name='select_hidden_post_status']").val();
        }
        filter_args['page_number'] = parseInt($this.attr('data-page'));
        if (parent.find("[name='page_item']").length > 0) {
            filter_args['page_item'] = parseInt(parent.find("[name='page_item']").val());
        }
        if (target.length > 0) {
            jQuery.ajax({
                type: 'POST', url: abpet_admin_data.ajax_url, data: {
                    "action": "abpet_reload_post_list", "filter_args": filter_args, 'nonce': abpet_admin_data.nonce
                }, beforeSend: function () {
                    abpet_spinner(parent);
                    abpet_toast_msg(abpet_admin_data.msg.post_loading);
                }, success: function (response) {
                    if (response.data && response.data.hasOwnProperty('html')) {
                        target.html(response.data.html);
                    }
                    abpet_spinner_remove(parent);
                    abpet_toast_msg(response.data.msg, response.data.type);
                }, error: function (xhr) {
                    abpet_ajx_error(xhr, parent);
                }
            });
        } else {
            parent.find('.post_tab').trigger('click');
        }
    }
});
(function ($) {
    "use strict";
    //==========ticket config=================//
    abpet_parent.on('abp_trigger', '.ticket_configuration .add_new_hook', function () {
        abpet_ticket_type_selection();
    });
    abpet_parent.on('abp_trigger', '.ticket_configuration.configuration_content', function () {
        load_total($(this));
        load_price($(this));
    });
    abpet_parent.on('change', '.ticket_configuration [name="ticket_name[]"] ', function () {
        abpet_ticket_type_selection();
    });
    abpet_parent.on('change', '.abpet_ticket [name="seat_type"]', function () {
        load_ticket_type($(this));
    });
    abpet_parent.on('abp_trigger', '.abpet_ticket [name="display_ticket_type"]', function () {
        load_ticket_type($(this));
    });
    abpet_parent.on('change', '.sp_selection_area [name="sp_id[]"]', function () {
        let sp_id = $(this).val();
        let parent = $(this).closest('tr');
        let grand_parent = $(this).closest('.ticket_configuration');
        let $targetContainer = parent.find('.ticket_type_details');
        $targetContainer.empty();
        parent.find('.row_total').html('');
        if (!sp_id || !abpet_sp_info[sp_id]) {
            return;
        }
        let total_seat = 0;
        let ticketTypes = abpet_sp_info[sp_id];
        let html = '<div class="_gap_xs_f_wrap">';
        Object.keys(ticketTypes).forEach(function (key) {
            let item = ticketTypes[key];
            let label = item.label || '';
            let color = item.color || '#333';
            let icon = item.icon || '';
            let img = item.img || '';
            let icon_emoji = '';
            if (img && $.isNumeric(icon) && icon > 0) {
                icon_emoji = '<div class="abp_image"><img class="_img_control"  src="' + img + '" alt="#"></div>';
            } else {
                if (abpet_emoji_check(icon)) {
                    icon_emoji = '<span>' + icon + '</span>';
                } else {
                    icon_emoji = '<span class="' + icon + '"></span>';
                }
            }
            let seatCount = parseInt(item.seat, 10) || 0;
            total_seat = total_seat + seatCount;
            if (label !== '') {
                html += `<div class="abp_tag">`;
                html += `<span class="_abp_gap_xxs" style="color: ${color}">`;
                if (icon_emoji !== '') {
                    html += ` ${icon_emoji}`;
                }
                html += ` ${label}`;
                html += `</h6>`;
                html += `<span class="_color_theme"> ( ${seatCount}</span> ) `;
                html += `</div>`;
            }
        });
        html += '</div>';
        parent.find('.row_total').html(total_seat);
        parent.find('.sp_id_change').attr('onclick', "abpet_popup_open_global('view_sp','" + sp_id + "')");
        $targetContainer.html(html);
        load_total(grand_parent);
        load_price(grand_parent);
    });
    function load_total(grand_parent) {
        let total_seat_count = 0;
        grand_parent.find('.row_total').each(function () {
            total_seat_count = total_seat_count + parseInt($(this).html(), 0);
        }).promise().done(function () {
            grand_parent.find('.total_ticket').html(total_seat_count);
        });
    }
    function load_ticket_type($this) {
        let parent = $this.closest('.abpet_ticket');
        let type = abpet_parent.find("[name='seat_type']").val();
        let display_ticket_type = abpet_parent.find("[name='display_ticket_type']").val();
        let target = parent.find('.ticket_configuration');
        let post_id = abpet_parent.find("[name='abpet_post_id']").val();
        $.ajax({
            type: 'POST', url: abpet_admin_data.ajax_url, data: {
                "action": 'abpet_type_switch', 'type': type, 'display_ticket_type': display_ticket_type, 'post_id': post_id, 'nonce': abpet_admin_data.nonce
            }, beforeSend: function () {
                abpet_spinner(parent);
                abpet_toast_msg(abpet_admin_data.msg.type_switch);
            }, success: function (response) {
                if (target && target.length > 0 && response.data && response.data.hasOwnProperty('html')) {
                    target.html(response.data.html);
                }
                abpet_spinner_remove(parent);
                abpet_toast_msg(response.data.msg, response.data.type);
            }, error: function (xhr) {
                abpet_spinner_remove(parent);
                if (xhr.response && xhr.response.data) {
                    abpet_toast_msg(xhr.response.data.msg, xhr.response.data.type);
                }
            }
        });
    }
    function load_price(parent) {
        let ticketTypesMap = {};
        let exit_ticket_types = {};
        let display_ticket_type = abpet_parent.find("[name='display_ticket_type']").val();
        if (display_ticket_type === 'on') {
            parent.find('.ticket_selection_area tr').each(function () {
                let ticket_val = $(this).find('[name="ticket_name[]"]').val();
                let price_val = $(this).find('[name="ticket_price[]"]').val();
                if (ticket_val) {
                    exit_ticket_types[ticket_val] = price_val || 0;
                }
            });
            parent.find('[name="sp_id[]"]').each(function () {
                let sp_id = $(this).val();
                if (sp_id && abpet_sp_info[sp_id]) {
                    let ticket_ids = Object.keys(abpet_sp_info[sp_id]);
                    ticket_ids.forEach(ticket_id => {
                        if (!ticketTypesMap[ticket_id]) {
                            let item = abpet_sp_info[sp_id][ticket_id];
                            ticketTypesMap[ticket_id] = {
                                id: ticket_id,
                                label: (item && item.label) ? item.label : ticket_id,
                                icon: (item && item.icon) ? item.icon + ' ' : ''
                            };
                        }
                    });
                }
            });
            let html = '';
            Object.values(ticketTypesMap).forEach(function (ticket) {
                let price = (exit_ticket_types[ticket.id] !== undefined) ? exit_ticket_types[ticket.id] : 0;
                html += `<tr><th>${ticket.label}<input type="hidden" name="ticket_name[]" value="${ticket.id}"/></th>
                                        <td><label><input data-required type="number" class="_form_control validation_price" name="ticket_price[]" placeholder="EX: 15" value="${price}"/></label></td></tr>`;
            });
            parent.find('.ticket_selection_area').html(html);
        }
    }
    //==========Orders list=================//
    abpet_parent.on('submit', 'div.abpet_orders form.abp_search_form', function (e) {
        e.preventDefault();
        let parent = $(this).closest('.abpet_orders');
        let target = parent.find('.order_list');
        let formData = new FormData(this);
        if (parent.find('[data-page].abp_active').length > 0) {
            formData.append('page_number', parseInt(parent.find('[data-page].abp_active').attr('data-page')));
        }
        formData.append('page_item', parseInt(parent.find("[name='page_item']").val()));
        formData.append('status', parent.find('.order_status_menu [data-status].abp_active').attr('data-status'));
        formData.append('action', 'abpet_load_order_list');
        formData.append('nonce', abpet_admin_data.nonce);
        $.ajax({
            type: 'POST', url: abpet_admin_data.ajax_url, contentType: false, processData: false, data: formData,
            beforeSend: function () {
                abpet_spinner(parent);
                abpet_toast_msg(abpet_admin_data.msg.order_loading);
            },
            success: function (response) {
                abpet_spinner_remove(parent);
                if (response.data) {
                    if (response.data.hasOwnProperty('html')) {
                        target.html(response.data.html).promise().done(function () {
                            abpet_init(target);
                        });
                    }
                    abpet_toast_msg(response.data.msg, response.data.type);
                }
            }, error: function (xhr) {
                abpet_ajx_error(xhr, parent);
            }
        });
    });
    abpet_parent.on('click', 'div.abpet_orders .order_status_menu button[data-status]', function () {
        let $this = $(this);
        if (!$this.hasClass('abp_active')) {
            $this.closest('.order_status_menu').find('[data-status].abp_active').removeClass('abp_active').promise().done(function () {
                $this.addClass('abp_active').promise().done(function () {
                    $this.closest('.abpet_orders').find('form.abp_search_form').submit();
                });
            });
        }
    });
    abpet_parent.on('click', 'div.abpet_orders button.item_cancel', function () {
        let $this = $(this);
        let parent = $(this).closest('.abpet_orders');
        let item_id = $this.attr('data-item_id');
        if (confirm(abpet_admin_data.msg.confirm_delete + ' \n\n' + abpet_admin_data.msg.confirm_ok + ' \n ' + abpet_admin_data.msg.confirm_cancel)) {
            $.ajax({
                type: 'POST', url: abpet_admin_data.ajax_url, data: {
                    "action": "abpet_item_cancel", 'item_id': item_id, 'nonce': abpet_admin_data.nonce
                }, beforeSend: function () {
                    abpet_spinner(parent);
                    abpet_toast_msg(abpet_admin_data.msg.deleting, 'error');
                }, success: function (response) {
                    abpet_spinner_remove(parent);
                    if (response.data) {
                        abpet_toast_msg(response.data.msg, response.data.type);
                    }
                    $this.closest('.abpet_orders').find('form.abp_search_form').submit();
                }, error: function (xhr) {
                    abpet_ajx_error(xhr, parent);
                }
            });
        }
    });
    abpet_parent.on('click', 'div.abpet_orders .order_list .pagination_area button[data-page]', function () {
        let $this = $(this);
        if (!$this.hasClass('abp_active')) {
            let parent = $(this).closest('.order_list');
            parent.find('[data-page].abp_active').removeClass('abp_active').promise().done(function () {
                $this.addClass('abp_active').promise().done(function () {
                    $this.closest('.abpet_orders').find('form.abp_search_form').submit();
                });
            });
        }
    });
}(jQuery));
//==============Empty title check /image selection/add_new_delete============================//
(function ($) {
    'use strict';
    $(document).ready(function () {
        new ABPET_Multi_Selection('div.abpet_admin .post_feature', abpet_feature);
        new ABPET_Multi_Selection('div.abpet_admin .related_item', abpet_related_info);
        new ABPET_Multi_Selection('div.abpet_admin .abpet_category', abpet_category);
        new ABPET_Multi_Selection('div.abpet_admin .abpet_organizer', abpet_organizer);
        new ABPET_Multi_Selection('div.abpet_admin .abpet_brand', abpet_brand);
        new ABPET_Multi_Selection('div.abpet_admin .abpet_location', abpet_location);
        //=========Color Picker==============//
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.wp-picker-container').length) {
                $('.wp-picker-container.wp-picker-active').find('.wp-color-result').trigger('click');
            }
        });
    });
    //========= Empty title check==============//
    $(document).on('click', '#publish, .editor-post-publish-button', function (e) {
        let hasPostIdInput = $('input[name="abpet_post_id"]').length > 0;
        if (hasPostIdInput) {
            let title = $('#title').val() || $('.editor-post-title__input').val();
            if (!title || title.trim().length === 0) {
                alert('Title empty! Please enter a title before updating.');
                e.preventDefault();
                return false;
            }
        }
    });
    //==================image selection========================//
    $(document).on('click', 'div.abpet_admin .add_image_multi', function () {
        let parent = $(this).closest('.multiple_image_area');
        wp.media.editor.send.attachment = function (props, attachment) {
            let attachment_id = attachment.id;
            let attachment_url = attachment.url;
            let html = '<div class="multiple_image_item" data-image-id="' + attachment_id + '"><span class="fas fa-times _circle_icon_xs remove_image_multi"></span>';
            html += '<img class="_img_control" src="' + attachment_url + '" alt="' + attachment_id + '"/>';
            html += '</div>';
            parent.find('.multiple_image').append(html);
            let value = parent.find('.multiple_image_ids').val();
            value = value ? value + ',' + attachment_id : attachment_id;
            parent.find('.multiple_image_ids').val(value);
        }
        wp.media.editor.open($(this));
        return false;
    });
    $(document).on('click', 'div.abpet_admin .remove_image_multi', function () {
        let parent = $(this).closest('.multiple_image_area');
        let current_parent = $(this).closest('.multiple_image_item');
        let img_id = current_parent.data('image-id');
        current_parent.remove();
        let all_img_ids = parent.find('.multiple_image_ids').val();
        all_img_ids = all_img_ids.replace(',' + img_id, '')
        all_img_ids = all_img_ids.replace(img_id + ',', '')
        all_img_ids = all_img_ids.replace(img_id, '')
        parent.find('.multiple_image_ids').val(all_img_ids);
    });
    $(document).on('click', 'div.abpet_admin .icon_image_selection_area .icon_delete', function () {
        let parent = $(this).closest('.icon_image_selection_area');
        parent.find('input[type="hidden"]').val('');
        parent.find('[data-add-icon]').removeAttr('class');
        parent.find('.icon_item').slideUp('fast');
        parent.find('.image_icon_select_area').slideDown('fast');
    });
    $(document).on('click', 'div.abpet_admin button.image_select', function () {
        let $this = $(this);
        let parent = $this.closest('.icon_image_selection_area');
        wp.media.editor.send.attachment = function (props, attachment) {
            let attachment_id = attachment.id;
            let attachment_url = attachment.url;
            parent.find('input[type="hidden"]').val(attachment_id);
            parent.find('.icon_item').slideUp('fast');
            parent.find('img').attr('src', attachment_url);
            parent.find('.image_item').slideDown('fast');
            parent.find('.image_icon_select_area').slideUp('fast');
        }
        wp.media.editor.open($this);
        return false;
    });
    $(document).on('click', 'div.abpet_admin .icon_image_selection_area .image_delete', function () {
        let parent = $(this).closest('.icon_image_selection_area');
        parent.find('input[type="hidden"]').val('');
        parent.find('img').attr('src', '');
        parent.find('.image_item').slideUp('fast');
        parent.find('.image_icon_select_area').slideDown('fast');
    });
    //=========add_new_delete ==============//
    abpet_parent.on('click', '.delete_hook', function () {
        if (confirm(abpet_admin_data.msg.confirm_delete + ' \n\n' + abpet_admin_data.msg.confirm_ok + ' \n ' + abpet_admin_data.msg.confirm_cancel)) {
            let deleteArea = $(this).closest('.delete_area');
            let parent = $(this).closest('.configuration_content');
            deleteArea.slideUp(250, function () {
                $(this).remove();
                if (parent.find('.insertable_area .delete_area').length === 0) {
                    parent.find('.hide_on_load').slideUp(250);
                }
                abpet_toast_msg(abpet_admin_data.msg.delete_success);
                parent.trigger('abp_trigger');
            });
        }
    });
    abpet_parent.on('click', '.add_new_hook', function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        let parent = $(this).closest('.configuration_content');
        let target_element = $(this).next('.abp_hidden');
        if (target_element.length === 0) {
            target_element = parent.children('.abp_hidden');
        }
        if (target_element.length === 0) {
            target_element = parent.find('.abp_hidden').first();
        }
        let item_html = target_element.find('.hidden_content').html();
        if (!item_html || item_html === "undefined" || item_html === " ") {
            target_element = parent.find('.abp_hidden').first();
            item_html = target_element.find('.hidden_content').html();
        }
        let $item = $(item_html);
        let unique_id = 'abp_' + Date.now();
        if (target_element.attr('data-hidden_id') !== undefined) {
            if (item_html && item_html !== "undefined" && item_html.trim() !== "") {
                let current_id = $item.find('.hidden_id').val();
                $item.find('.hidden_id').val(unique_id);
                $item.find('input, select, textarea').each(function () {
                    let current_name = $(this).attr('name');
                    if (current_name && current_id) {
                        let regex = new RegExp(current_id, 'g');
                        let new_name = current_name.replace(regex, unique_id);
                        $(this).attr('name', new_name);
                    }
                });
            }
        }
        let $insertable_area = parent.find('.insertable_area').first();
        $insertable_area.append($item);
        let target = $item.hasClass('delete_area') ? $item : $item.find('.delete_area');
        if (target.length === 0) {
            target = $item;
        }
        target.find('.edit_area').slideDown('fast');
        parent.find('.hide_on_load').slideDown('fast');
        abpet_init(target);
        setTimeout(function () {
            abpet_color_picker_init(target);
            abpet_wp_editor_init(target);
        }, 50);
        $(this).trigger('abp_trigger');
    });
    abpet_parent.on('click', '.edit_hook', function () {
        $(this).closest('.delete_area').toggleClass('active').find('.edit_area').slideToggle('fast');
        //$(this).closest('.delete_area').find('.edit_area').slideToggle('fast');
    });
    abpet_parent.on('keyup change', '[data-pass]', function () {
        let input_value = $(this).val();
        let input_id = $(this).attr('data-pass');
        $(this).closest('.delete_area').find("[data-paste='" + input_id + "']").each(function () {
            $(this).html(input_value);
        });
    });
})(jQuery);
//=================select icon=========================//
(function ($) {
    'use strict';
    let abpet_target_popup = $(document).find('div.abpet_admin .popup_icon');
    let abpet_category_list = abpet_target_popup.find('.dropdown_list');
    let abpet_search_field = abpet_target_popup.find('.abp_dropdown .abp_icon_search');
    let abpet_icon_title = abpet_target_popup.find('.item_icon_title');
    let abpet_icon_area = abpet_target_popup.find('.item_icon_area');
    let abpet_item_loader = abpet_target_popup.find('.item_loader');
    let search_result_icon = [];
    let total_icon = 0;
    let abpet_json_icon = [];
    $.getJSON(abpet_admin_data.icon_url, function (data) {
        abpet_json_icon = data;
        load_icon_category_list();
    }).fail(function () {
        abpet_icon_area.html('Nothing Found !');
    });
    $(document).on('click', 'div.abpet_admin .icon_image_selection_area button.icon_add', function () {
        load_icon_list();
    });
    $(document).on('abp_trigger', 'div.abpet_admin .abp_dropdown .abp_icon_search_hidden', function () {
        let search_value = $(this).val().toLowerCase().trim();
        if (search_value === '' || search_value.length > 2) {
            load_icon_list();
        }
    });
    abpet_search_field.keyup(function () {
        let search_value = $(this).val().toLowerCase().trim();
        if (search_value === '' || search_value.length > 2) {
            load_icon_list();
        }
    });
    abpet_search_field.change(function () {
        let search_value = $(this).val().toLowerCase().trim();
        if (search_value === '' || search_value.length > 2) {
            load_icon_list();
        }
    });
    abpet_target_popup.find('.popup_close').click(function () {
        abpet_search_field.val('').trigger('change');
        abpet_target_popup.find('.icon_item').removeClass('abp_active');
    });
    abpet_target_popup.on('click', '.icon_item', function () {
        let parent = $('[data-active-popup]').closest('.icon_image_selection_area');
        let icon_class = $(this).data('icon-class');
        if (icon_class) {
            parent.find('input[type="hidden"]').val(icon_class);
            parent.find('.image_icon_select_area').slideUp('fast');
            parent.find('.image_item').slideUp('fast');
            parent.find('.icon_item').slideDown('fast');
            if (abpet_emoji_check(icon_class)) {
                parent.find('[data-add-icon]').removeAttr('class').html(icon_class);
            } else {
                parent.find('[data-add-icon]').removeAttr('class').addClass(icon_class).html('');
            }
            abpet_target_popup.find('.icon_item').removeClass('abp_active');
            abpet_target_popup.find('.popup_close').trigger('click');
        }
    });
    // ─── get search icon array / initial array───────────
    function get_icon_array() {
        let pool = [];
        let search_value = abpet_search_field.val().toLowerCase().trim();
        if (search_value) {
            $.each(abpet_json_icon, function (i, group) {
                if (group.category.toLowerCase().includes(search_value)) {
                    $.each(group.icons, function (iconKey, iconLabel) {
                        let match = iconLabel.match(/#(.*?)#/);
                        let finalLabel = match ? match[1] : iconLabel;
                        pool.push({key: iconKey, label: finalLabel});
                    });
                    return pool;
                } else {
                    if (i !== 0) {
                        $.each(group.icons, function (iconKey, iconLabel) {
                            if (iconLabel.toLowerCase().includes(search_value)) {
                                let match = iconLabel.match(/#(.*?)#/);
                                let finalLabel = match ? match[1] : iconLabel;
                                pool.push({key: iconKey, label: finalLabel});
                            }
                        });
                    }
                }
            });
        } else {
            let group = abpet_json_icon[0];
            if (!group) return [];
            $.each(group.icons, function (iconKey, iconLabel) {
                pool.push({key: iconKey, label: iconLabel});
            });
        }
        return pool;
    }
    // ─── load input category ───────────
    function load_icon_category_list() {
        let category_list = $('<ul>').addClass('_abp');
        $.each(abpet_json_icon, function (i, group) {
            let current_count = Object.keys(group.icons).length;
            if (i !== 0) {
                total_icon += current_count;
            }
            let text = group.category;
            let category_li = $('<li>').attr('data-value', text).attr('data-text', text);
            $('<span>').addClass('_mar_r_xxs').text(group.emoji).appendTo(category_li);
            $('<span>').text(text).appendTo(category_li);
            $('<span>').text('( ' + current_count + ' )').appendTo(category_li);
            category_li.appendTo(category_list);
        });
        category_list.appendTo(abpet_category_list);
        abpet_spinner(abpet_item_loader);
    }
    function load_icon_list() {
        abpet_icon_area.empty();
        search_result_icon = get_icon_array();
        if (search_result_icon.length === 0) {
            abpet_icon_area.html('Nothing Found !');
            updateCount();
            return;
        }
        $.each(search_result_icon, function (i, item) {
            let $item = $('<div>').addClass('icon_item').attr('title', item.label).attr('data-icon-class', item.key);
            let $preview;
            if (abpet_emoji_check(item.key)) {
                $preview = $('<span>').text(item.key);
            } else {
                $preview = $('<span>').addClass(item.key);
            }
            $item.append($preview);
            $item.append($('<i>').text(item.label));
            $item.appendTo(abpet_icon_area);
        });
        updateCount();
    }
    function updateCount() {
        let search_value = abpet_search_field.val();
        search_value = search_value ? search_value : 'Selected Icon'
        abpet_icon_title.text(search_value + ' : ' + search_result_icon.length + ' / ' + total_icon + ' icons');
    }
})(jQuery);
//=========== Multi selection start=================//
class ABPET_Multi_Selection {
    constructor(parentSelector, dataSource) {
        this.parent = document.querySelector(parentSelector);
        if (!this.parent) return;
        this.dataSource = dataSource;
        this.hiddenInput = this.parent.querySelector('.selected_area input[type="hidden"]');
        this.selectedList = this.parent.querySelector('.selected_list');
        this.init();
    }
    init() {
        this.searchEl = this.parent.querySelector('.item_search');
        this.featureListEl = this.parent.querySelector('.selection_list');
        this.loadPreSelected();
        this.bindEvents();
        this.render();
    }
    bindEvents() {
        if (this.searchEl) {
            ['focusin', 'click'].forEach(eventType => {
                this.searchEl.addEventListener(eventType, (e) => {
                    e.stopPropagation();
                    this.featureListEl?.classList.add('active');
                });
            });
            this.searchEl.addEventListener('input', () => this.render());
        }
        document.addEventListener('click', (e) => {
            if (!e.target.closest(this.parent.className.split(' ').map(c => '.' + c).join(''))) {
                this.featureListEl?.classList.remove('active');
            }
        });
    }
    loadPreSelected() {
        if (!this.hiddenInput || !this.selectedList) return;
        let hiddenVal = this.hiddenInput.value;
        let preIds = hiddenVal ? hiddenVal.split(',').map(s => s.trim()).filter(Boolean) : [];
        if (preIds.length > 0) {
            this.selectedList.innerHTML = '';
            preIds.forEach(id => {
                let f = this.dataSource.find(x => String(x.id) === String(id));
                if (!f) return;
                this.appendSelectedItem(f);
            });
        }
    }
    getSelectedIds() {
        let ids = [];
        this.parent.querySelectorAll('.selected_item').forEach(el => {
            let id = el.getAttribute('data-id');
            if (id) ids.push(id);
        });
        return ids;
    }
    render() {
        if (!this.featureListEl) return;
        let q = this.searchEl ? this.searchEl.value.toLowerCase() : '';
        let selectedIds = this.getSelectedIds();
        let available = this.dataSource.filter(f => {
            return selectedIds.indexOf(String(f.id)) === -1 && f.label.toLowerCase().indexOf(q) !== -1;
        });
        if (available.length === 0) {
            this.featureListEl.innerHTML = `<div class="item_empty">${abpet_admin_data.msg.no_item}</div>`;
            return;
        }
        this.featureListEl.innerHTML = available.map(f => {
            let icon_text = abpet_emoji_check(f.icon) ? `<span>${f.icon}</span>` : `<span class="${f.icon}"></span>`;
            let label = f.value ? f.label + '-' + f.value : f.label;
            return `
                <div class="selection_item" data-id="${f.id}">
                    <div class="_gap_xxs">${icon_text}${label}</div>
                    <span class="fa-solid fa-plus fs-add"></span>
                </div>
            `;
        }).join('');
        this.featureListEl.querySelectorAll('.selection_item').forEach(item => {
            item.addEventListener('click', () => {
                this.selectItem(item.getAttribute('data-id'));
            });
        });
    }
    selectItem(id) {
        let f = this.dataSource.find(x => String(x.id) === String(id));
        if (!f) return;
        let placeholder = this.selectedList.querySelector('.item_empty');
        if (placeholder) placeholder.remove();
        this.appendSelectedItem(f);
        this.updateHiddenField();
        this.render();
        setTimeout(() => {
            this.featureListEl?.classList.add('active');
        }, 0);
    }
    appendSelectedItem(f) {
        let div = document.createElement('div');
        div.className = 'selected_item';
        div.setAttribute('data-id', f.id);
        let icon_text = abpet_emoji_check(f.icon) ? `<span>${f.icon}</span>` : `<i class="${f.icon}"></i>`;
        let label = f.value ? f.label + '-' + f.value : f.label;
        div.innerHTML = `
            <div class="_gap_xs">${icon_text}${label}</div>
            <span class="item_remove">❌</span>
        `;
        div.querySelector('.item_remove').addEventListener('click', (e) => {
            e.stopPropagation();
            this.removeItem(f.id);
        });
        this.selectedList.appendChild(div);
    }
    removeItem(id) {
        let item = this.selectedList.querySelector(`.selected_item[data-id="${id}"]`);
        if (item) item.remove();
        if (!this.selectedList.querySelector('.selected_item')) {
            this.selectedList.innerHTML = `<div class="item_empty">${abpet_admin_data.msg.no_item_selected}</div>`;
        }
        this.updateHiddenField();
        this.render();
    }
    updateHiddenField() {
        if (this.hiddenInput) {
            this.hiddenInput.value = this.getSelectedIds().join(',');
        }
    }
}
