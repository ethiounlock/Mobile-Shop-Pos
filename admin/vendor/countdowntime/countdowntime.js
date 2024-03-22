(function ($) {
  "use strict";

  // Function to get the time remaining until the deadline
  function getTimeRemaining(endtime) {
    const t = Date.parse(endtime) - Date.parse(new Date());
    const seconds = Math.floor((t / 1000) % 60);
    const minutes = Math.floor((t / 1000 / 60) % 60);
    const hours = Math.floor((t / (1000 * 60 * 60)) % 24);
    const days = Math.floor(t / (1000 * 60 * 60 * 24));

    return {
      total: t,
      days: days,
      hours: hours,
      minutes: minutes,
      seconds: seconds
    };
  }

  // Function to initialize the clock
  function initializeClock(id, endtime) {
    const daysSpan = $(".days");
    const hoursSpan = $(".hours");
    const minutesSpan = $(".minutes");
    const secondsSpan = $(".seconds");

    // Method to update the clock
    function updateClock() {
      const time_remaining = getTimeRemaining(endtime);

      daysSpan.html(`${time_remaining.days}`);
      hoursSpan.html(`${("0" + time_remaining.hours).slice(-2)}`);
      minutesSpan.html(`${("0
