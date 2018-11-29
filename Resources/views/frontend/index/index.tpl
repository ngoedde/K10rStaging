{extends file='parent:frontend/index/index.tpl'}
{block name='frontend_index_before_page'}
    {block name="frontend_k10r_staging_badge"}
        {include file='frontend/_includes/messages.tpl' type='info' content="{s name='message' namespace='frontend/k10r_staging/badge'}You are currently in the STAGING system.{/s}"}
    {/block}

    {$smarty.block.parent}
{/block}
