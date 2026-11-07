const API = "/mini_github/backend/api";
console.log("AUTH.JS LOADED");
const registerForm = document.getElementById("signup-form");
const loginForm = document.getElementById("login-form");
const message = document.getElementById("message");

if (registerForm) {
  console.log("REGISTER FORM FOUND");
  registerForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    console.log("REGISTER SUBMITTED");

    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;
    const terms = registerForm.querySelector('input[name="terms"]').checked;

    if (password !== confirmPassword) {
      message.textContent = "Passwords do not match";
      return;
    }

    const data = {
      username: document.getElementById("username").value,
      email: document.getElementById("email").value,
      password: password,
      confirmPassword: confirmPassword,
      terms: terms,
    };

    try {
      const response = await fetch(`${API}/register.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      message.textContent = result.message;

      if (result.success) {
        window.location.href = "../index.html";
      }
    } catch (error) {
      message.textContent = "Server connection failed";
    }
  });
}


if (loginForm) {
  loginForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const data = {
      username: document.getElementById("username").value.trim(),
      password: document.getElementById("password").value
    };

    try {
      const response = await fetch(`${API}/login.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      });

      const result = await response.json();

      message.textContent = result.message;

      if (result.success) {
        window.location.href = "../index.html";
      }
    } catch (error) {
      message.textContent = "Server connection failed";
    }
  });
}


const logoutButton = document.getElementById("logoutButton");

if (logoutButton) {
  logoutButton.addEventListener("click", async function () {
    try {
      const response = await fetch(`${API}/logout.php`, {
        method: "POST",
      });

      const result = await response.json();

      if (result.success) {
        window.location.href = "../register/login.html";
      }
    } catch (error) {
      if (message) {
        message.textContent = "Server connection failed";
      }
    }
  });
}
