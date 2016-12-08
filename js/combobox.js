$(function () {
    $.widget("custom.combobox", {
        _create: function () {
            if (this.element.is('.comboboxed'))
                return;
            this.wrapper = $("<span>")
                    .addClass("custom-combobox")
                    .insertAfter(this.element);

            this.element.css({opacity: 0,
                display: "inline-block",
                marginTop: "2%",
                position: "absolute",
                height: "0px !important"}).addClass('comboboxed');
            this._createAutocomplete();
            this._createShowAllButton();
        },
        _createAutocomplete: function () {
            var selected = this.element.children(":selected"),
                    value = selected.val() ? selected.text() : "";
            this.input = $("<input>")
                    .attr('placeholder',this.element.find('option').eq('0').text())
                    .appendTo(this.wrapper)
                    .val(value)
                    .attr("title", "")
                    .addClass("custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left")
                    .autocomplete({
                        delay: 0,
                        minLength: 0,
                        source: $.proxy(this, "_source")
                    })
                    .tooltip({
                        classes: {
                            "ui-tooltip": "ui-state-highlight"
                        }
                    });

            this._on(this.input, {
                autocompleteselect: function (event, ui) {
                    ui.item.option.selected = true;
                    this._trigger("select", event, {
                        item: ui.item.option
                    });
                },
                autocompletechange: "_removeIfInvalid"
            });
        },
        _createShowAllButton: function () {
            var input = this.input,
                    wasOpen = false,
                    ele = this.element;
            $("<a>")
                    .attr("tabIndex", -2)
                    .attr("title", "add")
                    .tooltip()
                    .appendTo(this.wrapper)
                    .button({
                        icons: {
                            primary: "ui-icon-circle-plus"
                        },
                        text: false
                    })
                    .removeClass("ui-corner-all").on("click", function () {
                        ele.trigger('edit');
                    });
            $("<a>")
                    .attr("tabIndex", -1)
                    .attr("title", "Show All Items")
                    .tooltip()
                    .appendTo(this.wrapper)
                    .button({
                        icons: {
                            primary: "ui-icon-triangle-1-s"
                        },
                        text: false
                    })
                    //.removeClass( "ui-corner-all" )
                    //.addClass( "custom-combobox-toggle ui-corner-right" )
                    .on("mousedown", function () {
                        wasOpen = input.autocomplete("widget").is(":visible");
                    })
                    .on("click", function () {
                        input.trigger("focus");

                        // Close if already visible
                        if (wasOpen) {
                            return;
                        }

                        // Pass empty string as value to search for, displaying all results
                        input.autocomplete("search", "");
                    });
        },
        _source: function (request, response) {
            var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
            response(this.element.children("option").map(function () {
                var text = $(this).text();
                if (this.value && (!request.term || matcher.test(text)))
                    return {
                        label: text,
                        value: text,
                        option: this
                    };
            }));
        },
        _removeIfInvalid: function (event, ui) {

            // Selected an item, nothing to do
            if (ui.item) {
                return;
            }

            // Search for a match (case-insensitive)
            var value = this.input.val(),
                    valueLowerCase = value.toLowerCase(),
                    valid = false;
            this.element.children("option").each(function () {
                if ($(this).text().toLowerCase() === valueLowerCase) {
                    this.selected = valid = true;
                    return false;
                }
            });

            // Found a match, nothing to do
            if (valid) {
                return;
            }

            // Remove invalid value
            this.input
                    .val("")
                    .attr("title", value + " didn't match any item")
                    .tooltip("open");
            this.element.val("");
            this._delay(function () {
                this.input.tooltip("close").attr("title", "");
            }, 2500);
            this.input.autocomplete("instance").term = "";
        },
        _destroy: function () {
            this.wrapper.remove();
            this.element.show();
        }
    });
});