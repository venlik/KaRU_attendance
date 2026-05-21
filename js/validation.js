// js/validation.js
function validateRegistration() {
    var regNo = document.getElementById("reg_number").value.trim();
    var fullName = document.getElementById("fullname").value.trim();
    var email = document.getElementById("email").value.trim();
    var phone = document.getElementById("phone").value.trim();
    var password = document.getElementById("password").value.trim();

    if (regNo === "" || fullName === "" || email === "" || phone === "" || password === "") {
        alert("All fields are required!");
        return false;
    }

    // Registration Number format (e.g., S123/0001X/24)
    if (!/^[a-zA-Z]\d{3}\/\d{4}[a-zA-Z]?\/\d{2}$/.test(regNo)) {
        alert("Invalid Registration Number format! Example: S123/0001X/24");
        return false;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert("Please enter a valid email address!");
        return false;
    }

    if (!/^0[17]\d{8}$/.test(phone)) {
        alert("Phone number must be a valid Kenyan number (e.g., 0712345678)!");
        return false;
    }

    if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/.test(password)) {
        password.length < 8 ?
            alert("Password must be at least 8 characters long!") :
            alert("Weak password, password should contain mixed case characters, digits and special characters");
        return false;
    }

    return true;
}