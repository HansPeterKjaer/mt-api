module.exports = function (el, selector) {
    while ((el = el.parentElement) && !el.matches(selector));
    return el;
}