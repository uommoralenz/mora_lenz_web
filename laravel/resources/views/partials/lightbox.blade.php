{{--
    One shared photo viewer per page. site.js opens it from any element with
    data-lightbox-open and a data-photos list of {src, caption}.
--}}
<div class="lightbox" data-lightbox hidden role="dialog" aria-modal="true" aria-label="Photo viewer">
    <div class="lightbox__bar">
        <div class="lightbox__heading">
            <span class="lightbox__title" data-lightbox-title></span>
            <span class="lightbox__count" data-lightbox-count></span>
        </div>
        <button type="button" class="lightbox__btn" data-lightbox-close aria-label="Close viewer">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <div class="lightbox__stage">
        <button type="button" class="lightbox__btn lightbox__nav lightbox__nav--prev" data-lightbox-prev aria-label="Previous photo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <figure class="lightbox__figure">
            <img class="lightbox__img" data-lightbox-img alt="">
            <figcaption class="lightbox__caption" data-lightbox-caption></figcaption>
        </figure>
        <button type="button" class="lightbox__btn lightbox__nav lightbox__nav--next" data-lightbox-next aria-label="Next photo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>

    <div class="lightbox__strip" data-lightbox-strip></div>
</div>
