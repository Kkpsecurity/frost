<style>
    #preloaders {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: rgba(0, 0, 0, 0.6);
        z-index: 9999;
    }

    .loading-text,
    .loading-progress {
        margin-top: 16px;
    }
</style>
<div id="preloaders">
    <div class="frost-loader">
        <div class="inner"></div>
    </div>

    <div class="loading-text">Loading...</div>

    <div class="loading-progress">
        <div class="loading-progress-bar"></div>
    </div>
</div>
