function selectImage(href, name, element)
{
    var myWin = window.open(href, name);
    myWin.opener = self;
    myWin.element = element;
}

function newPage(catID)
{
alert('new page');
    parent.edit.location.href = "pagenew.php?cat=" + catID;
    location.href = "menu.php?cat=" + catID;
}

function newCat(catID)
{
    parent.edit.location.href = "catnew.php?cat=" + catID;
    location.href = "menu.php?cat=" + catID;
}

function deletePage(pageID)
{
    if(confirm('Wollen Sie diese Seite wirklich löschen?'))
    {
        parent.edit.location.href = "pagedelete.php?page=" + pageID;
        // window.setTimeout("location.reload()",2000);
    }
}

function changeToCat(pageID)
{
    if(confirm('Wollen Sie diese Seite wirklich in eine Kategorie umwandeln??'))
    {
        parent.edit.location.href = "changetocat.php?page=" + pageID;
        // window.setTimeout("location.reload()",2000);
    }
}

function deleteCat(catID)
{
    if(confirm('Wollen Sie diese Kategorie wirklich löschen?'))
    {
        parent.edit.location.href = "catdelete.php?cat=" + catID;
        // window.setTimeout("location.reload()",2000);
    }
}

function deleteElement(element)
{
    if(confirm('Wollen Sie diesen Abschnitt wirklich löschen?'))
    {
        element.value='yes';
        document.save.submit();
    }
}

function insertElement(element, val)
{
    element.value = val;
    document.save.submit();
}

function setValue(key, val)
{
    key.value = val;
    document.save.submit();
}

function doSubmit()
{


    if(document.save.autosubmit.checked)
    {
        document.save.submit();
    }
}
