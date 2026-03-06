document.getElementById("filter_role").addEventListener("change", function () {
  const role = this.value;
  fetch("admin-fetch-script?role=" + role)
    .then((res) => res.text())
    .then((data) => {
      document.getElementById("members_body").innerHTML = data;
    });
});
