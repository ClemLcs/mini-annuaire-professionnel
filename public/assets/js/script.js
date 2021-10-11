const error = document.querySelector('.flash-error');
const warning = document.querySelector('.flash-warning');
const success = document.querySelector('.flash-success');

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

if(success){
    swal({
        icon: "success",
        title: "Success",
        text: success.children[0].innerHTML
    });
}