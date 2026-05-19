
const GS = {
  inicioSolicitud: function () {
    $("#divLoading").css("display", "flex");
  },
  finSolicitud: function () {
    $("#divLoading").css("display", "none");
  },
  modalAdvertencia: function (mensaje) {
    swal("Advertencia!", mensaje, "warning");
  },
  modalError: function (mensaje) {
    swal("Advertencia!", mensaje, "error");
  },
  modalCorrecto: function (mensaje) {
    swal("Proceso exitoso.", mensaje, "success");
  },

  alertaInfo: function (titulo, mensaje) {
    toastr.info(mensaje, titulo, {
      positionClass: "toast-top-right",
      timeOut: 5e3,
      closeButton: !0,
      debug: !1,
      newestOnTop: !0,
      progressBar: !0,
      preventDuplicates: !0,
      onclick: null,
      showDuration: "300",
      hideDuration: "1000",
      extendedTimeOut: "1000",
      showEasing: "swing",
      hideEasing: "linear",
      showMethod: "fadeIn",
      hideMethod: "fadeOut",
      tapToDismiss: !1,
    });
  },

modalConfirmacion: function (titulo, mensaje, confirmCallback) {
  Swal.fire({
      title: titulo || "¿Estás seguro?",
      html: mensaje || "Esta acción no se puede revertir.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Sí, Proceder",
      cancelButtonText: "No, Cancelar"
  }).then((result) => {
      console.log("Resultado de confirmación:", result);
      if (result.value) {
          console.log("Ejecutando confirmCallback()");
          if (typeof confirmCallback === "function") {
              confirmCallback();
          }
      }
  });
},


  modalSinRequisitos: function (mensaje) {
    swal({
      title: "Advertencia!",
      html: true,
      text: mensaje,
      icon: "warning",
    });
  },

   modalSinRequisitos: function (mensaje){
        swal(
            {
                title: 'Advertencia!',
                html: true,
                text: mensaje,
                icon: 'warning'
            }
        );
    },
    b64toBlob: function(b64Data, contentType='', sliceSize=512) {
    const byteCharacters = atob(b64Data);
    const byteArrays = [];

    for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
      const slice = byteCharacters.slice(offset, offset + sliceSize);

      const byteNumbers = new Array(slice.length);
      for (let i = 0; i < slice.length; i++) {
        byteNumbers[i] = slice.charCodeAt(i);
      }

      const byteArray = new Uint8Array(byteNumbers);
      byteArrays.push(byteArray);
    }

    const blob = new Blob(byteArrays, { type: contentType });
    return blob;
  },
  toastSuccess: function (message) {
    Toastify({
      text: message,
      duration: 3000,
      gravity: "top",
      position: "right",
      backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
    }).showToast();
  },
  toastError: function (message) {
    Toastify({
      text: message,
      duration: 3000,
      gravity: "top",
      position: "right",
      backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)",
    }).showToast();
  },

  confirmarArea: function (mensaje, callback) {
    Swal.fire({
      title: "¿Estás seguro?",
      text: mensaje,
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Aceptar",
      cancelButtonText: "Regresar",
      confirmButtonColor: "#157347",
      cancelButtonColor: "#d33",
    }).then((result) => {
      if (result.value) {
        callback();
      }
    });
  },
};
