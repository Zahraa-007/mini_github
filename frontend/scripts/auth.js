const API = "/mini_github/backend/api";

const registerForm = document.getElementById("signup_form");
const loginForm = document.getElementById("login_form");
const message = document.getElementById("message");

if (registerForm) {
  registerForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const data = {
      name: document.getElementById("name").value,
      email: document.getElementById("email").value,
      password: document.getElementById("password").value,
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
        window.location.href = "dashboard.html";
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
      email: document.getElementById("email").value,
      password: document.getElementById("password").value,
    };

    try {
      const response = await fetch(`${API}/login.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      message.textContent = result.message;

      if (result.success) {
        window.location.href = "dashboard.html";
      }
    } catch (error) {
      message.textContent = "Server connection failed";
    }
  });
}

if (document.getElementById("dashboard")) {
  async function checkLogin() {
    try {
      const response = await fetch(`${API}/me.php`);
      const result = await response.json();

      if (!result.success) {
        window.location.href = "login.html";
        return;
      }

      document.getElementById("welcome").textContent =
        `Welcome, ${result.data.name}`;
    } catch (error) {
      window.location.href = "login.html";
    }
  }

  checkLogin();
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
        window.location.href = "login.html";
      }
    } catch (error) {
      message.textContent = "Server connection failed";
    }
  });
}
