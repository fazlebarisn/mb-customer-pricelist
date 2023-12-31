
// Create urlParams query string
var urlParams = new URLSearchParams(window.location.search);

// Get value of single parameter
var sectionName = urlParams.get('pageno');

if(sectionName){

    sectionName = parseInt(sectionName) + 1;
    var url = 'edit.php?page=mb-customer-sync&page-number=' + sectionName;
    setTimeout(() => {
        window.location.replace(url);
    }, 6000);

}

