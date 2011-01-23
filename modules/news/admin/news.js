function doSubmit()
{
        document.save.submit();
}
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
