<div id='popup_cart'>
<div class="popup_cart_container">
        <span id="close_pop_cart">Close</span>
        <p>The lowest price is <span class="lowprice">{$lowest_price}{$cartCurrency->sign}</span>, please provide more</p>
        <a class="index" href="{$urls.pages.index}">Continue shopping</a>
    </div>
</div>
<style>
    body#cart #popup_cart.active {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #popup_cart {
        position: fixed;
        z-index: 1000000;
        top: 0;
        left: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.19);
        width: 100vw;
        height: 100vh;
        display: none;
    }

    #popup_cart .popup_cart_container {
        width: 50%;
        height: 200px;
        border-radius: 3px;
        background-color: white;
        opacity: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        position: relative;
        animation-name: reveal;
        animation-timing-function: ease-in-out;
        animation-duration: 1s;
    }

    @keyframes reveal {
        0% {
            opacity: 0;
            translate: 0px -600px;
        }

        20% {
            opacity: 0.3;
        }

        40% {
            opacity: 0.5;
        }

        60% {
            opacity: 0.7;
        }

        80% {
            opacity: 1;
            translate: 0px 20px;
        }

        100% {
            opacity: 1;
            translate: 0px 0px;
        }
    }

    #close_pop_cart {
        position: absolute;
        top: 10px;
        right: 10px;
        color: black;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
    }

    .popup_cart_container p {
        color: rgb(49, 49, 49);
        font-size: 17px;    
    }
    .lowprice{
        color: rgb(22, 22, 22);
    }
</style>