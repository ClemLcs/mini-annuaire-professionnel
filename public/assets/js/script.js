const error = document.querySelector('.flash-error');
const warning = document.querySelector('.flash-warning');

if (error){
    swal({
        icon: "error",
        title: "Une erreur s'est produite",
        text: error.children[0].innerHTML
    });
}

if(warning){
    swal({
        icon: "warning",
        title: "Une erreur s'est produite",
        text: warning.children[0].innerHTML
    });
}