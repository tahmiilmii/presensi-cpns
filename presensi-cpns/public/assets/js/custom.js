function showToast(
  icon,
  title,
  msg,
  timer = 3000,
  onclick = null,
  closeBtn = false
) {
  const Toast = Swal.mixin({
    toast: true,
    position: "top-right",
    showConfirmButton: false,
    showCloseButton: closeBtn,
    timer: timer,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;

      if (onclick != null) {
        toast.onclick = onclick;
      }
    },
  });

  Toast.fire({
    icon: icon,
    title: title,
    html: msg,
  });
}

function bsAlert(selector = "#alert", type, title, message) {
  const sel = $(selector);

  sel.html(`
    <!--begin::Alert-->
    <div class="alert alert-${type} d-flex align-items-center p-5">
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-${type}">${title}</h4>
            <span>${message}</span>
        </div>
    </div>
  `);
}

function base64ToBlobAndDownload(base64Data, fileName, mimetype) {
  const base64String = base64Data.replace(/^data:.+?;base64,/, "");

  const byteCharacters = atob(base64String);

  const byteArray = new Uint8Array(byteCharacters.length);
  for (let i = 0; i < byteCharacters.length; i++) {
    byteArray[i] = byteCharacters.charCodeAt(i);
  }

  const blob = new Blob([byteArray], { type: mimetype });

  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = fileName;

  link.click();

  URL.revokeObjectURL(link.href);
}

function select2(selector, options = {}) {
  $(selector).select2({
    theme: "bootstrap-5",
    width: "100%",
    ...options,
  });
}

function triggerDownload(url) {
  window.open(url, "_blank");
}
