let overLayer = document.getElementById('overLayer');
let userPanel = document.getElementById('userPanel');
let userPanelOpen = document.getElementById('userPanel-open');

let aside_menu = document.getElementById('aside_menu');
let collapse_aside = document.getElementById('collapse_aside');
let mobil_collapse_aside = document.getElementById('mobil_collapse_aside');

userPanelOpen.addEventListener('click', function () {
    overLayer.classList.add('active');
    userPanel.classList.add('open');

});

collapse_aside.addEventListener('click', () => {
    document.body.classList.toggle('aside_collapsed')
});

mobil_collapse_aside.addEventListener('click', () => {
    aside_menu.classList.add('open');
    overLayer.classList.add('active');

});

// OverLayer
overLayer.addEventListener('click', function () {
    overLayer.classList.remove('active');
    userPanel.classList.remove('open');
    aside_menu.classList.remove('open');

});