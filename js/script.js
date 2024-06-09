
document.getElementById('registerForm').addEventListener('submit', function (event) {
    var errors = [];
    var name = document.forms['registerForm']['name'].value.trim();
    var password = document.forms['registerForm']['password'].value;
    var email = document.forms['registerForm']['email'].value.trim();

    if (name === '' || !/^[a-zA-Z0-9_]+$/.test(name)) {
        errors.push('Имя может содержать только буквы, цифры и символы подчеркивания.');
    }

    if (password.length < 6) {
        errors.push('Пароль должен быть не менее 6 символов.');
    }

    if (email === '' || !/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(email)) {
        errors.push('Неверный формат email.');
    }

    if (errors.length > 0) {
        event.preventDefault();
        var errorContainer = document.getElementById('errorContainer');
        errorContainer.innerHTML = '';
        errors.forEach(function (error) {
            var errorElement = document.createElement('p');
            errorElement.textContent = error;
            errorContainer.appendChild(errorElement);
        });
    }
});
