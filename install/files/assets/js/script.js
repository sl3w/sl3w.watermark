function showWait() {
    let loader = document.createElement('div');
    let style = document.createElement('style');

    loader.className = 'sl3w_watermark_loader';
    loader.innerHTML = '<div class="sl3w_watermark_loader-clock"></div>';

    style.innerHTML = '.sl3w_watermark_loader {\n' +
        '    position: fixed;\n' +
        '    width: 100%;\n' +
        '    min-width: 100%;\n' +
        '    height: 100%;\n' +
        '    min-height: 100%;\n' +
        '    top: 0;\n' +
        '    left: 0;\n' +
        '    z-index: 10000 !important;\n' +
        '    background-color: rgba(255, 255, 255, 0.75);\n' +
        '}\n' +
        '\n' +
        '.sl3w_watermark_loader-clock {\n' +
        '    border-radius: 58px;\n' +
        '    border: 3px solid #122072;\n' +
        '    height: 78px;\n' +
        '    width: 78px;\n' +
        '    position: absolute;\n' +
        '    top: 50%;\n' +
        '    left: 50%;\n' +
        '    margin-top: -39px;\n' +
        '    margin-left: -39px;\n' +
        '}\n' +
        '\n' +
        '.sl3w_watermark_loader-clock:after {\n' +
        '    content: "";\n' +
        '    position: absolute;\n' +
        '    background-color: #122072;\n' +
        '    top: 2px;\n' +
        '    left: 48%;\n' +
        '    height: 37px;\n' +
        '    width: 4px;\n' +
        '    border-radius: 5px;\n' +
        '    transform-origin: 50% 97%;\n' +
        '    -o-transform-origin: 50% 97%;\n' +
        '    -ms-transform-origin: 50% 97%;\n' +
        '    -webkit-transform-origin: 50% 97%;\n' +
        '    -moz-transform-origin: 50% 97%;\n' +
        '    animation: sl3wWMgrdAnimate 0.8s linear infinite;\n' +
        '    -o-animation: sl3wWMgrdAnimate 0.8s linear infinite;\n' +
        '    -ms-animation: sl3wWMgrdAnimate 0.8s linear infinite;\n' +
        '    -webkit-animation: sl3wWMgrdAnimate 0.8s linear infinite;\n' +
        '    -moz-animation: sl3wWMgrdAnimate 0.8s linear infinite;\n' +
        '}\n' +
        '\n' +
        '.sl3w_watermark_loader-clock:before {\n' +
        '    content: "";\n' +
        '    position: absolute;\n' +
        '    background-color: #122072;\n' +
        '    top: 6px;\n' +
        '    left: 48%;\n' +
        '    height: 34px;\n' +
        '    width: 4px;\n' +
        '    border-radius: 5px;\n' +
        '    transform-origin: 50% 94%;\n' +
        '    -o-transform-origin: 50% 94%;\n' +
        '    -ms-transform-origin: 50% 94%;\n' +
        '    -webkit-transform-origin: 50% 94%;\n' +
        '    -moz-transform-origin: 50% 94%;\n' +
        '    animation: sl3wWMptAnimate 4.8s linear infinite;\n' +
        '    -o-animation: sl3wWMptAnimate 4.8s linear infinite;\n' +
        '    -ms-animation: sl3wWMptAnimate 4.8s linear infinite;\n' +
        '    -webkit-animation: sl3wWMptAnimate 4.8s linear infinite;\n' +
        '    -moz-animation: sl3wWMptAnimate 4.8s linear infinite;\n' +
        '}\n' +
        '\n' +
        '@keyframes sl3wWMgrdAnimate {\n' +
        '    0% {\n' +
        '        transform: rotate(0deg);\n' +
        '    }\n' +
        '    100% {\n' +
        '        transform: rotate(360deg);\n' +
        '    }\n' +
        '}\n' +
        '\n' +
        '@-o-keyframes sl3wWMgrdAnimate {\n' +
        '    0% {\n' +
        '        -o-transform: rotate(0deg);\n' +
        '    }\n' +
        '    100% {\n' +
        '        -o-transform: rotate(360deg);\n' +
        '    }\n' +
        '}\n' +
        '\n' +
        '@-ms-keyframes sl3wWMgrdAnimate {\n' +
        '    0% {\n' +
        '        -ms-transform: rotate(0deg);\n' +
        '    }\n' +
        '    100% {\n' +
        '        -ms-transform: rotate(360deg);\n' +
        '    }\n' +
        '}\n' +
        '\n' +
        '@-webkit-keyframes sl3wWMgrdAnimate {\n' +
        '    0% {\n' +
        '        -webkit-transform: rotate(0deg);\n' +
        '    }\n' +
        '    100% {\n' +
        '        -webkit-transform: rotate(360deg);\n' +
        '    }\n' +
        '}\n' +
        '\n' +
        '@-moz-keyframes sl3wWMgrdAnimate {\n' +
        '    0% {\n' +
        '        -moz-transform: rotate(0deg);\n' +
        '    }\n' +
        '    100% {\n' +
        '        -moz-transform: rotate(360deg);\n' +
        '    }\n' +
        '}\n' +
        '\n' +
        '@keyframes sl3wWMptAnimate {\n' +
        '    0% {\n' +
        '        transform: rotate(0deg);\n' +
        '    }\n' +
        '    100% {\n' +
        '        transform: rotate(360deg);\n' +
        '    }\n' +
        '}\n' +
        '\n' +
        '@-o-keyframes sl3wWMptAnimate {\n' +
        '    0% {\n' +
        '        -o-transform: rotate(0deg);\n' +
        '    }\n' +
        '    100% {\n' +
        '        -o-transform: rotate(360deg);\n' +
        '    }\n' +
        '}\n' +
        '\n' +
        '@-ms-keyframes sl3wWMptAnimate {\n' +
        '    0% {\n' +
        '        -ms-transform: rotate(0deg);\n' +
        '    }\n' +
        '    100% {\n' +
        '        -ms-transform: rotate(360deg);\n' +
        '    }\n' +
        '}\n' +
        '\n' +
        '@-webkit-keyframes sl3wWMptAnimate {\n' +
        '    0% {\n' +
        '        -webkit-transform: rotate(0deg);\n' +
        '    }\n' +
        '    100% {\n' +
        '        -webkit-transform: rotate(360deg);\n' +
        '    }\n' +
        '}\n' +
        '\n' +
        '@-moz-keyframes sl3wWMptAnimate {\n' +
        '    0% {\n' +
        '        -moz-transform: rotate(0deg);\n' +
        '    }\n' +
        '    100% {\n' +
        '        -moz-transform: rotate(360deg);\n' +
        '    }\n' +
        '}';

    document.body.appendChild(loader);
    document.body.appendChild(style);
}

function hideWait() {
    document.querySelectorAll('body .sl3w_watermark_loader')[0].remove();
}

function addWatermarkByItemId(element_id, iblock_id) {
    console.log('Запрос на нанесение водяного знака. ID элемента: ' + element_id);

    let btn = document.getElementById('sl3w-add-watermark-btn');

    btn.textContent = 'В процессе...';
    setBtnColor(btn, '#e9bd5b');
    showWait();

    BX.ajax({
        type: 'GET',
        url: '/ajax/sl3w.watermark/add_watermark.php?element_id=' + element_id + '&iblock_id=' + iblock_id,
        async: true,
        dataType: 'json',
        onsuccess: function (response) {
            console.log(response);

            if (response.watermarked) {
                setBtnColor(btn, '#8fbc8f');

                btn.textContent = 'Водяной знак нанесен';

                setTimeout(function () {
                    window.location.reload();
                }, 100);
            } else {
                setBtnColor(btn, '#e9967a');

                btn.textContent = 'Ошибка при обработке';
            }

            hideWait();
        },
        onfailure: function () {
            setBtnColor(btn, '#e9967a');

            btn.textContent = 'Ошибка при обработке';

            hideWait();
        }
    });

    btn.href = 'javascript:void(0)';
}

function addWatermarkBySectionId(section_id, iblock_id) {
    console.log('Запрос на нанесение водяного знака. ID раздела: ' + section_id);

    let btn = document.getElementById('sl3w-add-watermark-btn');

    btn.textContent = 'В процессе...';
    setBtnColor(btn, '#e9bd5b');
    showWait();

    BX.ajax({
        type: 'GET',
        url: '/ajax/sl3w.watermark/add_watermark.php?section_id=' + section_id + '&iblock_id=' + iblock_id,
        async: true,
        dataType: 'json',
        onsuccess: function (response) {
            console.log(response);

            if (response.watermarked) {
                setBtnColor(btn, '#8fbc8f');

                btn.textContent = 'Водяной знак нанесен';

                setTimeout(function () {
                    window.location.reload();
                }, 100);
            } else {
                setBtnColor(btn, '#e9967a');

                btn.textContent = 'Ошибка при обработке';
            }

            hideWait();
        },
        onfailure: function () {
            setBtnColor(btn, '#e9967a');

            btn.textContent = 'Ошибка при обработке';

            hideWait();
        }
    });

    btn.href = 'javascript:void(0)';
}

function setBtnColor(btn, color) {
    // btn.style.setProperty('background-image', 'none', 'important');

    btn.style.setProperty('background-color', color, 'important');
    btn.style.setProperty('background-image', 'linear-gradient(bottom, ' + color + ', #fff)', 'important');
    btn.style.setProperty('background-image', '-mos-linear-gradient(bottom, ' + color + ', #fff)', 'important');
    btn.style.setProperty('background-image', '-o-linear-gradient(bottom, ' + color + ', #fff)', 'important');
    btn.style.setProperty('background-image', '-webkit-linear-gradient(bottom, ' + color + ', #fff)', 'important');

    btn.style.setProperty('cursor', 'default', 'important');
}