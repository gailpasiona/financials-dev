$(function() {
        // Highlight the active nav link.
            var url = window.location.pathname;
            var filename = url.substr(url.lastIndexOf('/') + 1);
            if ( $('.subnavbar a[href$="' + filename + '"]').parents(".mainnav").length == 1 ) { 
                $('.subnavbar a[href$="' + filename + '"]').parents("li").addClass("active");
               // YES, the child element is inside the parent

            } else {
             $('.subnavbar a[href$="' + filename + '"]').parent().addClass("active");
               // NO, it is not inside
           }
});

