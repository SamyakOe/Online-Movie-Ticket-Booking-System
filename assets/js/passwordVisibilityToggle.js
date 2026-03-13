function setupPasswordToggle(passwordInput) {
  const input = passwordInput.querySelector(".password");
  const toggle = passwordInput.querySelector(".visibilityToggle");

  toggle.addEventListener("click", () => {
    const isHidden = input.type === "password";
    input.type = isHidden ? "text" : "password";
    toggle.innerText = isHidden ? "visibility_off" : "visibility";
  });
}

// Apply to all input-box elements
document.querySelectorAll(".password-input").forEach(setupPasswordToggle);
