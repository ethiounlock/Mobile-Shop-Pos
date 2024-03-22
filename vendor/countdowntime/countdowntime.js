(function ($) {
  "use strict";

  // Gets the remaining time in milliseconds until the given endtime
  function getTimeRemaining(endtime) {
    if (!endtime) {
      console.error("Invalid endtime");
      return null;
    }

    var t = Date.parse(endtime) - Date.parse(new Date());
    var seconds = Math.floor((t / 1000) % 60);
    var minutes = Math.floor((t / 1000 / 60) % 60);
    var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
    var days = Math.floor(t / (1000 * 60 * 60 * 24));

    return {
      total: t,
      days: t ? days : null,
      hours: t ? hours : null,
      minutes: t ? minutes : null,
      seconds: t ? seconds : null,
    };
  }

  // Initializes the countdown clock
  function initializeClock(id, endtime) {
    const daysSpan = $(".days");
    const hoursSpan = $(".hours");
    const minutesSpan = $(".minutes");
    const secondsSpan = $(".seconds");

    function updateClock() {
      const t = getTimeRemaining(endtime);

      if (t === null) {
        clearInterval(timeinterval);
        return;
      }

      daysSpan.html(`${t.days}`);
      hoursSpan.html(`${("0" + t.hours).slice(-2)}`);
      minutesSpan.html(`${("0" + t.minutes).slice
