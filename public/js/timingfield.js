(function ($) {

    var TimingField = function (element, options) {
        this.elem = $(element);
        this.disabled = false;
        this.settings = $.extend({}, $.fn.timingfield.defaults, options);
        this.tpl = $($.fn.timingfield.template);

        this.init();
    };

    TimingField.prototype = {
        init: function () {
            this.elem.after(this.tpl);
            this.elem.hide();
            var timeoutId = 0;

            if (this.elem.is(':disabled')) {
                this.disable();
            }

            this.getDays().value = this.tsToDays(this.elem.val());
            this.getHours().value = this.tsToHours(this.elem.val());
            this.getMinutes().value = this.tsToMinutes(this.elem.val());

            this.tpl.width(this.settings.width);
            this.tpl.find('.timingfield_days    .input-group-addon').text(this.settings.daysText);
            this.tpl.find('.timingfield_hours   .input-group-addon').text(this.settings.hoursText);
            this.tpl.find('.timingfield_minutes .input-group-addon').text(this.settings.minutesText);

            this.tpl.find('.timingfield_hours .timingfield_next')
                .on('mouseup', function () {
                    clearInterval(timeoutId);
                    return false;
                })
                .on('mousedown', function (e) {
                    timeoutId = setInterval($.proxy(this.upHour, this), 100);
                    return false;
                })
            ;

            // +/- triggers
            this.tpl.find('.timingfield_days    .timingfield_next').on('mousedown', $.proxy(this.upDay, this));
            this.tpl.find('.timingfield_days    .timingfield_prev').on('mousedown', $.proxy(this.downDay, this));
            this.tpl.find('.timingfield_hours   .timingfield_next').on('mousedown', $.proxy(this.upHour, this));
            this.tpl.find('.timingfield_hours   .timingfield_prev').on('mousedown', $.proxy(this.downHour, this));
            this.tpl.find('.timingfield_minutes .timingfield_next').on('mousedown', $.proxy(this.upMin, this));
            this.tpl.find('.timingfield_minutes .timingfield_prev').on('mousedown', $.proxy(this.downMin, this));

            // input triggers
            this.tpl.find('.timingfield_days    input').on('keyup', $.proxy(this.inputDay, this));
            this.tpl.find('.timingfield_hours   input').on('keyup', $.proxy(this.inputHour, this));
            this.tpl.find('.timingfield_minutes input').on('keyup', $.proxy(this.inputMin, this));

            // change on elem
            this.elem.on('change', $.proxy(this.change, this));
        },
        getDays: function () {
            return this.tpl.find('.timingfield_days input')[0];
        },
        getHours: function () {
            return this.tpl.find('.timingfield_hours input')[0];
        },
        getMinutes: function () {
            return this.tpl.find('.timingfield_minutes input')[0];
        },
        tsToDays: function (timestamp) {
            return parseInt(timestamp / 86400);
        },
        tsToHours: function (timestamp) {
            return parseInt(timestamp / 3600);
        },
        tsToMinutes: function (timestamp) {
            return parseInt((timestamp % 3600) / 60);
        },
        hmsToTimestamp: function (d, h, m) {
            return parseInt(d) * 86400 + parseInt(h) * 3600 + parseInt(m) * 60;
        },
        updateElem: function () {
            this.elem.val(this.hmsToTimestamp(
                this.getDays().value,
                this.getHours().value,
                this.getMinutes().value
            )).trigger("change");
        },
        upDay: function () {
            if (!this.disabled) {
                if (this.getDays().value < this.settings.maxDay) {
                    this.getDays().value = parseInt(this.getDays().value) + 1;
                    this.updateElem();
                    return true;
                }
            }
            return false;
        },
        downDay: function () {
            if (!this.disabled) {
                if (this.getDays().value > 0) {
                    this.getDays().value = parseInt(this.getDays().value) - 1;
                    this.updateElem();
                    return true;
                }
            }
            return false;
        },
        inputDay: function () {
            if (!this.disabled) {
                if (this.getDays().value < 0) {
                    this.getDays().value = 0;
                } else if (this.getDays().value > this.settings.maxDay) {
                    this.getDays().value = this.settings.maxDay;
                }
            }

            this.updateElem();
        },
        upHour: function () {
            if (!this.disabled) {
                if (this.getHours().value < this.settings.maxHour) {
                    this.getHours().value = parseInt(this.getHours().value) + 1;
                    this.updateElem();
                    return true;
                }
            }
            return false;
        },
        downHour: function () {
            if (!this.disabled) {
                if (this.getHours().value > 0) {
                    this.getHours().value = parseInt(this.getHours().value) - 1;
                    this.updateElem();
                    return true;
                }
            }
            return false;
        },
        inputHour: function () {
            if (!this.disabled) {
                if (this.getHours().value < 0) {
                    this.getHours().value = 0;
                } else if (this.getHours().value > this.settings.maxHour) {
                    this.getHours().value = this.settings.maxHour;
                }
            }

            this.updateElem();
        },
        upMin: function () {
            if (!this.disabled) {
                if (this.getMinutes().value < 59) {
                    this.getMinutes().value = parseInt(this.getMinutes().value) + 1;
                    this.updateElem();
                    return true;
                } else if (this.upHour()) {
                    this.getMinutes().value = 0;
                    this.updateElem();
                    return true;
                }
            }

            return false;
        },
        downMin: function () {
            if (!this.disabled) {
                if (this.getMinutes().value > 0) {
                    this.getMinutes().value = parseInt(this.getMinutes().value) - 1;
                    this.updateElem();
                    return true;
                } else if (this.downHour()) {
                    this.getMinutes().value = 59;
                    this.updateElem();
                    return true;
                }
            }

            return false;
        },
        inputMin: function () {
            if (!this.disabled) {
                if (this.getMinutes().value < 0) {
                    this.getMinutes().value = 0;
                } else if (this.getMinutes().value > 59) {
                    this.getMinutes().value = 59;
                }

                this.updateElem();
            }
        },
        disable: function () {
            this.disabled = true;
            this.tpl.find('input:text').prop('disabled', true);
        },
        enable: function () {
            this.disabled = false;
            this.tpl.find('input:text').prop('disabled', false);
        },
        change: function () {
            if (this.elem.is(':disabled')) {
                this.disable();
            } else {
                this.enable();
            }
        },
    };

    $.fn.timingfield = function (options) {
        // Iterate and reformat each matched element.
        return this.each(function () {
            var element = $(this);

            // Return early if this element already has a plugin instance
            if (element.data('timingfield')) return;

            var timingfield = new TimingField(this, options);

            // Store plugin object in this element's data
            element.data('timingfield', timingfield);
        });
    };

    $.fn.timingfield.defaults = {
        maxDay: 30,
        maxHour: 23,
        width: 263,
        daysText: 'D',
        hoursText: 'H',
        minutesText: 'M'
    };

    $.fn.timingfield.template = '<div class="timingfield">\
        <div class="timingfield_days">\
            <button type="button" class="timingfield_next btn btn-default btn-xs btn-block" tabindex="-1"><span class="glyphicon glyphicon-plus"></span></button>\
            <div class="input-group">\
                <input type="text" class="form-control">\
                <span class="input-group-addon"></span>\
            </div>\
            <button type="button" class="timingfield_prev btn btn-default btn-xs btn-block" tabindex="-1"><span class="glyphicon glyphicon-minus"></span></button>\
        </div>\
        <div class="timingfield_hours">\
            <button type="button" class="timingfield_next btn btn-default btn-xs btn-block" tabindex="-1"><span class="glyphicon glyphicon-plus"></span></button>\
            <div class="input-group">\
                <input type="text" class="form-control">\
                <span class="input-group-addon"></span>\
            </div>\
            <button type="button" class="timingfield_prev btn btn-default btn-xs btn-block" tabindex="-1"><span class="glyphicon glyphicon-minus"></span></button>\
        </div>\
        <div class="timingfield_minutes">\
            <button type="button" class="timingfield_next btn btn-default btn-xs btn-block" tabindex="-1"><span class="glyphicon glyphicon-plus"></span></button>\
            <span class="input-group">\
                <input type="text" class="form-control">\
                <span class="input-group-addon"></span>\
            </span>\
            <button type="button" class="timingfield_prev btn btn-default btn-xs btn-block" tabindex="-1"><span class="glyphicon glyphicon-minus"></span></button>\
        </div>\
    </div>';

}(jQuery));