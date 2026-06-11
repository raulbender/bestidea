<div id="rank-dropdown" class="drop-down-menu volt-animate">
    
    <a href="javascript:void(0)" onclick="VoltFeedSort.apply('recent')">
        <span>💡</span>
        <span><?= __('rooms.most_recent') ?? 'Most Recent' ?></span>
    </a>
    
    <hr>
    
    <a href="javascript:void(0)" onclick="VoltFeedSort.apply('comment')">
        <span>💬</span>
        <span><?= __('rooms.last_commented')?></span>
    </a>

    <hr>
    
    <a href="javascript:void(0)" onclick="VoltFeedSort.apply('votes')">
        <span>⭐</span>
        <span><?= __('rooms.top_rated')?></span>
    </a>
    
</div>