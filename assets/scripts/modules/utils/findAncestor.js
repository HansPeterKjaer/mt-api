module.exports = function (el, cls) {
    while ((el = el.parentElement) && !el.classList.contains(cls));
    return el;
}