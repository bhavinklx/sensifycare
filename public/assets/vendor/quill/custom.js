var quill = new Quill("#fullEditor", {
  theme: "snow",
  modules: {
    toolbar: toolbarOptions
  }
});

var toolbarOptions = [
  [{ font: [] }],
  [{ size: ['small', false, 'large', 'huge'] }],

  [{ header: [1, 2, 3, 4, 5, 6, false] }],

  ['bold', 'italic', 'underline', 'strike'],
  [{ color: [] }, { background: [] }],

  [{ script: 'sub' }, { script: 'super' }],
  [{ list: 'ordered' }, { list: 'bullet' }],
  [{ indent: '-1' }, { indent: '+1' }],

  [{ align: [] }],
  [{ direction: 'rtl' }],

  ['blockquote', 'code-block'],
  ['link', 'image', 'video', 'formula'],

  ['clean']
];
