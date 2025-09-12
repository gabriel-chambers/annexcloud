/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global gdsih_data, moment, ajaxurl*/
var gdsih_plugin_core;

;(function($, window, document, undefined) {
    gdsih_plugin_core = {
        init: function() {
            gdsih_plugin_core.dialogs.basic();

            if (gdsih_data.page === "csp-reports" || gdsih_data.page === "xxp-reports") {
                gdsih_plugin_core.dialogs.reports();

                gdsih_plugin_core.reports.init();
            }

            if (gdsih_data.page === "tools" && gdsih_data.panel === "export") {
                gdsih_plugin_core.tools.export();
            }
        },
        dialogs: {
            classes: function(extra) {
                var cls = "wp-dialog d4p-dialog gdsih-modal-dialog";

                if (extra !== "") {
                    cls += " " + extra;
                }

                return cls;
            },
            icons: function(id) {
                $(id).next().find(".ui-dialog-buttonset button").each(function() {
                    var icon = $(this).data("icon");

                    if (icon !== "") {
                        $(this).find("span.ui-button-text").prepend(gdsih_data["button_icon_" + icon]);
                    }
                });
            },
            defaults: function() {
                return {
                    width: 480,
                    height: "auto",
                    minHeight: 24,
                    autoOpen: false,
                    resizable: false,
                    modal: true,
                    closeOnEscape: false,
                    zIndex: 300000
                };
            },
            reports: function() {
                var dlg_events_details = $.extend({}, gdsih_plugin_core.dialogs.defaults(), {
                    width: 750,
                    dialogClass: gdsih_plugin_core.dialogs.classes("gdsih-dialog-hidex"),
                    buttons: [
                        {
                            id: "gdsih-log-ok",
                            class: "gdsih-dialog-button-ok gdsih-button-focus",
                            text: gdsih_data.dialog_button_ok,
                            data: {icon: "ok"},
                            click: function() {
                                $("#gdsih-dialog-log-details").wpdialog("close");
                            }
                        }
                    ]
                });

                $("#gdsih-dialog-log-details").wpdialog(dlg_events_details);

                gdsih_plugin_core.dialogs.icons("#gdsih-dialog-log-details");
            },
            basic: function() {
                var dlg_tools_wait = $.extend({}, gdsih_plugin_core.dialogs.defaults(), {
                    width: 640,
                    dialogClass: gdsih_plugin_core.dialogs.classes("gdsih-dialog-hidex"),
                    buttons: {
                        OK: function() {
                            $("#gdsih-dialog-please-wait").wpdialog("close");
                        }
                    }
                });

                $("#gdsih-dialog-please-wait").wpdialog(dlg_tools_wait);
            }
        },
        reports: {
            init: function() {
                $(".gdsih-log-view-event-data").click(function(e) {
                    e.preventDefault();

                    var id = $(this).attr("href").substr(1);

                    $("#gdsih-dialog-log-details .gdsih-inner-content").html($("#gdsih-event-content-" + id).html());
                    $("#gdsih-dialog-log-details").wpdialog("open");
                });
            }
        },
        tools: {
            export: function() {
                $("#gdsih-tool-export").click(function(e) {
                    e.preventDefault();

                    window.location = $("#gdsih-export-url").val();
                });
            }
        }
    };

    gdsih_plugin_core.init();
})(jQuery, window, document);
