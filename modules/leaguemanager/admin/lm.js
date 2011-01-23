function doSubmit()
{
        document.save.submit();
}

// menu master data functions
function doSelect()
{
        doSubmit();
}
function doEdit(name)
{
        parent.edit.location.href = name + ".php";
}
function doNew(name)
{
        parent.edit.location.href = name + ".php?new=new";
}






// edit functions
function doCancel(name)
{
        if(name != "")
                location.href = name+"List.php";
        else
                location.href = "empty.php";
}

function doCreate()
{
        document.save.job.value = "create";
        doSubmit();
}


function doModify()
{
        document.save.job.value = "modify";
        doSubmit();
}

function doDelete()
{
        if(confirm("Sicher"))
        {
                document.save.job.value = "delete";
                doSubmit();
        }
}

function checkDate(field)
{
}










/*
function selectImage(href, name, element)
{
    var myWin = window.open(href, name);
    myWin.opener = self;
    myWin.element = element;
}

function newPage(catID)
{
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
*/