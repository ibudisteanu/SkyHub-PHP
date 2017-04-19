        </tbody>
    </table>
<?=$bMasonryItem ? '</div> ' : ''?>


<?php
    $this->AlertsContainer->renderViewByName('g_msgAddForumCategorySuccess');
    $this->AlertsContainer->renderViewByName('g_msgAddForumCategoryError');
    $this->AlertsContainer->renderViewByName('g_msgAddForumCategoryWarning');
?>