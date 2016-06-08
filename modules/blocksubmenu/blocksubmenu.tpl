{if $hasParent == true && $items != ""}
    <div id="subCatMenu">
        {$items}
    </div>
    <div id="degrade_submenu"></div>

    <script>
        var nbItemSubMenu = "{$nbItems}"
    </script>
{/if}