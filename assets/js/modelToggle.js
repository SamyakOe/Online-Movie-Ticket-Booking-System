function openModel(url) {
    const model = document.getElementById("model");
    const frame = document.getElementById("model-frame");
    frame.src = url;
    model.style.display = "flex"
  }

  function closeModel() {
    const model = document.getElementById("model");
    const frame = document.getElementById("model-frame");
    model.style.display = "none"
    frame.src = "";
  }
  document.addEventListener("DOMContentLoaded", function() {
    const model = document.getElementById("model");
    model.addEventListener("click", function(event) {
      if (event.target === model) {
        closeModel();
      }
    })

  })
  document.addEventListener("keydown", function(event) {
    if (event.key === "Escape") {
      closeModel();
    }
  });