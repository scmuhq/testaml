function formatCardNumber(input) {
    let value = input.value.replace(/\s/g, '');
    value = value.replace(/[^0-9]/g, '');

    if (value.length > 19) {
        value = value.slice(0, 19);
    }

    let formattedValue = '';
    for (let i = 0; i < value.length; i++) {
        if (i > 0 && i % 4 === 0) {
            formattedValue += ' ';
        }
        formattedValue += value[i];
    }

    input.value = formattedValue;
}

function formatExpiry(input) {
    let value = input.value.replace(/\D/g, '');

    if (value.length >= 2) {
        value = value.slice(0, 2) + '/' + value.slice(2, 4);
    }

    input.value = value;
}

function formatPhoneNumber(input) {
    let value = input.value.replace(/\s/g, '');
    value = value.replace(/[^0-9]/g, '');

    if (value.length > 10) {
        value = value.slice(0, 10);
    }

    // Format français : 06 30 20 10 10 (groupes de 2)
    let formatted = '';
    for (let i = 0; i < value.length; i++) {
        if (i > 0 && i % 2 === 0) {
            formatted += ' ';
        }
        formatted += value[i];
    }

    input.value = formatted.trim();
}

function formatZipCode(input) {
    let value = input.value.replace(/[^0-9]/g, '');

    if (value.length > 5) {
        value = value.slice(0, 5);
    }

    input.value = value;
}
