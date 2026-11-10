const API = "/mini_github/backend/api";

async function apiRequest(endpoint, method = "GET", data = null) {
  const options = {
    method,
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/json",
    },
  };

  if (data) {
    options.body = JSON.stringify(data);
  }

  const response = await fetch(`${API}/${endpoint}`, options);
  const result = await response.json();

  if (!response.ok) {
    throw new Error(result.message || "Something went wrong");
  }

  return result;
}

const loginForm = document.getElementById("admin-login-form");

if (loginForm) {
  const message = document.getElementById("message");
  const showPassword = document.getElementById("show-password");
  const passwordInput = document.getElementById("password");

  showPassword?.addEventListener("change", () => {
    passwordInput.type = showPassword.checked ? "text" : "password";
  });

  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const username = document.getElementById("email").value.trim();
    const password = passwordInput.value;

    message.hidden = false;
    message.textContent = "Logging in...";

    try {
      const result = await apiRequest("admin_login.php", "POST", {
        username,
        password,
      });

      message.textContent = result.message;
      window.location.href = "admin_dashboard.html";
    } catch (error) {
      message.textContent = error.message;
    }
  });
}

const usersTable = document.querySelector("#users-table tbody");
const adminsTable = document.querySelector("#admins-table tbody");

if (usersTable && adminsTable) {
  const addForm = document.getElementById("add-account-form");
  const deleteForm = document.getElementById("delete-account-form");

  async function loadAccounts() {
    try {
      const result = await apiRequest("admin_users.php");

      const users = result.data.users;
      const admins = result.data.admins;

      usersTable.innerHTML = "";
      adminsTable.innerHTML = "";

      users.forEach((user) => {
        const row = document.createElement("tr");

        [user.username, user.email].forEach((value) => {
          const td = document.createElement("td");
          td.textContent = value;
          row.appendChild(td);
        });

        usersTable.appendChild(row);
      });

      admins.forEach((admin) => {
        const row = document.createElement("tr");

        [admin.username, admin.email].forEach((value) => {
          const td = document.createElement("td");
          td.textContent = value;
          row.appendChild(td);
        });

        adminsTable.appendChild(row);
      });

      document.querySelector("#users-table .count").textContent =
        `${users.length} accounts`;

      document.querySelector("#admins-table .count").textContent =
        `${admins.length} accounts`;
    } catch (error) {
      alert(error.message);

      if (error.message.includes("Admin access required")) {
        window.location.href = "admin.html";
      }
    }
  }

  addForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(addForm);

    const data = {
      action: "add",
      username: formData.get("username").trim(),
      email: formData.get("email").trim(),
      password: formData.get("password"),
      is_admin: formData.get("is_admin") === "on",
    };

    try {
      const result = await apiRequest("admin_account.php", "POST", data);

      alert(result.message);
      addForm.reset();
      await loadAccounts();
    } catch (error) {
      alert(error.message);
    }
  });

  deleteForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(deleteForm);
    const username = formData.get("delete_username").trim();
    const isAdmin = formData.get("delete_admin") === "on";

    if (!confirm(`Are you sure you want to delete ${username}?`)) {
      return;
    }

    try {
      const result = await apiRequest("admin_account.php", "POST", {
        action: "delete",
        username,
        is_admin: isAdmin,
      });

      alert(result.message);
      deleteForm.reset();
      await loadAccounts();
    } catch (error) {
      alert(error.message);
    }
  });

  loadAccounts();
}
