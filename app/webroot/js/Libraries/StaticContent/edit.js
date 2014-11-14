function StaticContentEdit(){
    this.uid = '';
}

StaticContentEdit.prototype = {

    //FUNCTION: save
    //Save the content as it appears in the editor
    save:function( uid ){

        //Grab the HTML content
        var htmlContent = jQuery( 'div.mce-edit-area > iframe' ).
            contents().
            find('body').
            html();

        console.log(htmlContent);

        //Run the JSON request
        jQuery.ajax({
            dataType:       'json',
            type:           'POST',
            url:            window.Paths.webroot + '/StaticContents/setHTML',
            data:           {
                uid:    uid,
                nuHTML: htmlContent
            },
            success:        function(){
                alert('saved');
            }
        });

    },

    //FUNCTION: setup
    //Setup the functionality
    setup:function(){

        var self = this;

        //We're deliberately not using on here to avoid attaching this content multiple times
        jQuery('input[type="button"].editStaticContentSaveButton').click(function(){
            self.save( jQuery(this).attr('uid') );
        });
    }

};


jQuery(document).ready( function(){
    var staticContentEdit = new StaticContentEdit();
    staticContentEdit.setup();
});
