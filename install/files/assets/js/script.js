function addWatermarkByItemId(element_id, iblock_id) {
    console.log('Запрос на наложение водяного знака. ID элемента: ' + element_id);

    let btn = document.getElementById('sl3w-add-watermark-btn');

    BX.ajax({
        type: 'GET',
        url: '/ajax/sl3w.watermark/add_watermark.php?element_id=' + element_id + '&iblock_id=' + iblock_id,
        async: false,
        dataType: 'json',
        onsuccess: function (response) {
            console.log(response);

            if (response.watermarked) {
                setBtnColor(btn, '#8fbc8f');

                btn.textContent = 'Водяной знак наложен';
            } else {
                setBtnColor(btn, '#e9967a');

                btn.textContent = 'Ошибка при обработке';
            }
        },
        onfailure: function () {
            setBtnColor(btn, '#e9967a');

            btn.textContent = 'Ошибка при обработке';
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