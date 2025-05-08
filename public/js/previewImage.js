    function previewImage(event) {
    const input = event.target;
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');

    if (input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = function(e) {
    previewImage.src = e.target.result;
    previewImage.style.display = 'block';
}

    reader.readAsDataURL(input.files[0]);
}
}
