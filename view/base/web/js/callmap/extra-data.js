define([
    'jquery',
    'jquery/jquery-ui',
], function ($) {
    'use strict';

    return {
        create: function () {
            this.openExtraDataModal();
        },

        openExtraDataModal: function () {
            var url = document.location.origin + '/debug/xhprof/detail';
            var modalWrapper = $('.function-extra-modal');
            $('.function-extra-data').click(function () {
                $.ajax({
                    url: url,
                    data: {
                        'token': $('.profile_token').html(),
                        'function': $(this).html()
                    }
                }).done(function(response) {
                    modalWrapper.html(response);
                    modalWrapper.dialog({
                        minWidth: $('#collector-content').width(),
                        modal: true,
                        draggable: false
                    });
                });
            });
        }
    };
});
