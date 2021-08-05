
// SweetAlert2, for more examples you can check out https://github.com/sweetalert2/sweetalert2
class pageDialogs {
    /*
     * SweetAlert2 demo functionality
     *
     */
    static sweetAlert2() {
        // Set default properties
        let toast = Swal.mixin({
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success m-1',
                cancelButton: 'btn btn-danger m-1',
                input: 'form-control'
            }
        });

        // Init a simple dialog on button click
        jQuery('.js-swal-simple').on('click', e => {
            toast.fire('Hi, this is just a simple message!');
        });

        // Init an success dialog on button click
        jQuery('.js-swal-success').on('click', e => {
            toast.fire('Success', 'Everything was updated perfectly!', 'success');
        });

        // Init an info dialog on button click
        jQuery('.js-swal-info').on('click', e => {
            toast.fire('Info', 'Just an informational message!', 'info');
        });

        // Init an warning dialog on button click
        jQuery('.js-swal-warning').on('click', e => {
            toast.fire('Warning', 'Something needs your attention!', 'warning');
        });

        // Init an error dialog on button click
        jQuery('.js-swal-error').on('click', e => {
            toast.fire('Oops...', 'Something went wrong!', 'error');
        });

        // Init an question dialog on button click
        jQuery('.js-swal-question').on('click', e => {
            toast.fire('Question', 'Are you sure about that?', 'question');
        });

        // Init an example confirm dialog on button click
        jQuery('.js-swal-confirm').on('click', e => {
            e.preventDefault();
            var link = e.currentTarget.href;
            var title = e.currentTarget.title;
            var caption = e.currentTarget.dataset.caption;
            toast.fire({
                title: title || 'Apakah anda yakin ?',
                text: caption || 'Data yang dihapus tidak dapat dikembalikan kembali',
                icon: 'warning',
                showCancelButton: true,
                customClass: {
                    confirmButton: 'btn btn-danger m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                html: false,
                closeOnConfirm: false,
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
            }).then(result => {
                if (result.isConfirmed) {
                    if (link) window.location.href = link;
                    toast.fire({
                        title: 'Terhapus!',
                        icon: 'success',
                        text: 'Data Berhasil dihapus.', 
                        showConfirmButton: false,
                    });
                    // result.dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                } 
                // else if (result.dismiss === 'cancel') {
                //     toast.fire('Cancelled', 'Your imaginary file is safe :)', 'error');
                // }
            });
        });

        jQuery('.js-swal-confirm-with-form').on('click', e => {
            e.preventDefault();
            var link = e.currentTarget.href;
            var title = e.currentTarget.title;
            var caption = e.currentTarget.dataset.caption;
            var form_id = e.currentTarget.dataset.form_id;
            var success_text = e.currentTarget.dataset.success_text;
            toast.fire({
                title: title || 'Apakah anda yakin ?',
                text: caption || '',
                icon: 'info',
                showCancelButton: true,
                customClass: {
                    confirmButton: 'btn btn-success m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
                html: false,
                closeOnConfirm: false,
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
            }).then(result => {
                if (result.isConfirmed) {
                    if (form_id) {
                        $('#'+form_id).attr('action', link);
                        $('#'+form_id).submit();
                    }
                    toast.fire({
                        title: 'Berhasil!',
                        icon: 'success',
                        text: success_text || '', 
                        showConfirmButton: false,
                    });
                    // result.dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                } 
                // else if (result.dismiss === 'cancel') {
                //     toast.fire('Cancelled', 'Your imaginary file is safe :)', 'error');
                // }
            });
        });

        // Init an example confirm alert on button click
        jQuery('.js-swal-custom-position').on('click', e => {
            toast.fire({
                position: 'top-end',
                title: 'Perfect!',
                text: 'Nice Position!',
                icon: 'success'
            });
        });
    }

    /*
     * Init functionality
     *
     */
    static init() {
        this.sweetAlert2();
    }
}

// Initialize when page loads
jQuery(() => { pageDialogs.init(); });
