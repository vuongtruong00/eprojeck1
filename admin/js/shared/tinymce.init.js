tinymce.init({
  selector: '#content',
  setup: function (editor) {
    editor.on('init', function (e) {
      const contentContainer = document.querySelector('#contenContainer');

      if (contentContainer) {
        let content = contentContainer.innerHTML;
        const replacements = {
          '&lt;': '<' ,
          '&gt;': '>' ,
          '&amp;': '&' 
        }
        const regex = new RegExp(Object.keys(replacements).join('|'), 'gi');
        content = content.replace(regex, match => replacements[match]);
        editor.setContent(content);
      }
      
    });
  },
  //https://stackoverflow.com/questions/37824335/slow-bad-performance-on-chrome-with-large-amount-of-html
  toolbar: 'undo redo | image code',
  plugins: 'preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
  imagetools_cors_hosts: ['picsum.photos'],
  menubar: 'file edit view insert format tools table help',
  toolbar: 'code | undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save | insertfile image media quickimage template link anchor codesample | ltr rtl',
  fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 24px 36px",
  toolbar_sticky: true,
  autosave_ask_before_unload: true,
  autosave_interval: "30s",
  autosave_prefix: "{path}{query}-{id}-",
  autosave_restore_when_empty: false,
  autosave_retention: "2m",
  image_advtab: true,
  content_css: '//www.tiny.cloud/css/codepen.min.css',
  link_list: [
  ],
  image_list: [
  ],
  image_class_list: [
    { title: 'None', value: '' },
    { title: 'Responsive', value: 'img-fluid' }
  ],
  importcss_append: true,
  templates: [
  ],
  template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
  template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
  height: 600,
  image_caption: true,
  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable | fontselect fontsizeselect formatselect',
  noneditable_noneditable_class: "mceNonEditable",
  toolbar_mode: 'sliding',
  contextmenu: "link image imagetools table"
});