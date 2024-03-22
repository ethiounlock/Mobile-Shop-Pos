/**
 * Daterangepicker
 * Version: 2.1.25
 * Author: Dan Grossman <http://www.dangrossman.info/>
 * Copyright: Copyright (c) 2012-2017 Dan Grossman. All rights reserved.
 * License: Licensed under the MIT license. See <http://www.opensource.org/licenses/mit-license.php>
 * Website: <http://www.daterangepicker.com/>
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['moment', 'jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory(require('moment'), require('jquery'));
    } else {
        root.daterangepicker = factory(root.moment, root.jQuery);
    }
}(this, function (moment, $) {

    function DateRangePicker(element, options, cb) {
        this.element = $(element);
        this.init(options, cb);
    }

    DateRangePicker.prototype = {
        constructor: DateRangePicker,

        init: function (options, cb) {
            this.parentEl = 'body';
            this.startDate = moment().startOf('day');
            this.endDate = moment().endOf('day');
            this.minDate = false;
            this.maxDate = false;
            this.dateLimit = false;
            this.autoApply = false;
            this.singleDatePicker = false;
            this.showDropdowns = false;
            this.showWeekNumbers = false;
            this.showISOWeekNumbers = false;
            this.showCustomRangeLabel = true;
            this.timePicker = false;
            this.timePicker24Hour = false;
            this.timePickerIncrement = 1;
            this.timePickerSeconds = false;
            this.linkedCalendars = true;
            this.autoUpdateInput = true;
            this.alwaysShowCalendars = false;
            this.ranges = {};

            this.opens = 'right';
            if (this.element.hasClass('pull-right')) this.opens = 'left';

            this.drops = 'down';
            if (this.element.hasClass('dropup')) this.drops = 'up';

            this.buttonClasses = 'btn btn-sm';
            this.applyClass = 'btn-success';
            this.cancelClass = 'btn-default';

            this.locale = {
                direction: 'ltr',
                format: moment.localeData().longDateFormat('L'),
                separator: ' - ',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
                weekLabel: 'W',
                customRangeLabel: 'Custom Range',
                daysOfWeek: moment.weekdaysMin(),
                monthNames: moment.monthsShort(),
                firstDay: moment.localeData().firstDayOfWeek()
            };

            this.callback = function () { };

            this.isShowing = false;
            this.leftCalendar = {};
            this.rightCalendar = {};

            if (typeof options === 'object' || options === null) {
                options = $.extend(this.element.data(), options);
            }

            this.parentEl = (options.parentEl && $(options.parentEl).length) ? $(options.parentEl) : $(this.parentEl);
            this.container = $(options.template).appendTo(this.parentEl);

            if (typeof options.locale === 'object') {
                this.locale = $.extend(this.locale, options.locale);
            }

            this.container.addClass(this.locale.direction);

            if (typeof options.startDate === 'string') this.startDate = moment(options.startDate, this.locale.format);
            if (typeof options.endDate === 'string') this.endDate = moment(options.endDate, this.locale.format);
            if (typeof options.minDate === 'string') this.minDate = moment(options.minDate, this.locale.format);
            if (typeof options.maxDate === 'string') this.maxDate = moment(options.maxDate, this.locale.format);

            if (this.minDate && this.startDate.isBefore(this.minDate)) this.startDate = this.minDate.clone();
            if (this.maxDate && this.endDate.isAfter(this.maxDate)) this.endDate = this.maxDate.clone();

            if (typeof options.applyClass === 'string') this.applyClass = options.applyClass;
            if (typeof options.cancelClass === 'string') this.cancelClass = options.cancelClass;
            if (typeof options.dateLimit === 'object') this.dateLimit = options.dateLimit;
            if (typeof options.opens === 'string') this.opens = options.opens;
            if (typeof options.drops === 'string') this.drops = options.drops;
            if (typeof options.showWeekNumbers === 'boolean') this.showWeekNumbers = options.showWeekNumbers;
            if (typeof options.showISOWeekNumbers === 'boolean') this.showISOWeekNumbers = options.showISOWeekNumbers;
            if (typeof options.buttonClasses === 'string') this.buttonClasses = options.buttonClasses;
            if (typeof options.buttonClasses === 'object') this.buttonClasses = options.buttonClasses.join(' ');
            if (typeof options.showDropdowns === 'boolean') this.showDropdowns = options.showDropdowns;
            if (typeof options.showCustomRangeLabel === 'boolean') this.showCustomRangeLabel = options.showCustomRangeLabel;
            if (typeof options.singleDatePicker === 'boolean') this.singleDatePicker = options.singleDatePicker;
            if (typeof options.timePicker === 'boolean') this.timePicker = options.timePicker;
            if (typeof options.timePickerSeconds === 'boolean') this.timePickerSeconds = options.timePickerSeconds;
            if (typeof options.timePickerIncrement === 'number') this.timePickerIncrement = options.timePickerIn
