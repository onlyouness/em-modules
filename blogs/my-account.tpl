{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Your account' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}








  <div class="row">
    <div class="links">

      <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="identity-link" href="{$urls.pages.identity}">
        <span class="link-item">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M22.9738 24.0871C21.9256 22.6994 20.5696 21.574 19.0125 20.7996C17.4554 20.0252 15.7398 19.623 14.0008 19.6246C12.2617 19.623 10.5461 20.0252 8.98898 20.7996C7.43188 21.574 6.07586 22.6994 5.02775 24.0871M22.9738 24.0871C25.019 22.2678 26.4614 19.8699 27.1125 17.2112C27.7636 14.5525 27.5911 11.7587 26.618 9.20028C25.6448 6.64186 23.9168 4.43972 21.6633 2.8859C19.4099 1.33208 16.7373 0.5 14 0.5C11.2627 0.5 8.59015 1.33208 6.33666 2.8859C4.08317 4.43972 2.35525 6.64186 1.38205 9.20028C0.408862 11.7587 0.23638 14.5525 0.88748 17.2112C1.53858 19.8699 2.9825 22.2678 5.02775 24.0871M22.9738 24.0871C20.5048 26.2893 17.3092 27.5043 14.0008 27.4996C10.6918 27.5046 7.49711 26.2897 5.02775 24.0871M18.5008 10.6246C18.5008 11.818 18.0266 12.9626 17.1827 13.8065C16.3388 14.6505 15.1942 15.1246 14.0008 15.1246C12.8073 15.1246 11.6627 14.6505 10.8188 13.8065C9.97486 12.9626 9.50075 11.818 9.50075 10.6246C9.50075 9.43108 9.97486 8.28649 10.8188 7.44258C11.6627 6.59866 12.8073 6.12456 14.0008 6.12456C15.1942 6.12456 16.3388 6.59866 17.1827 7.44258C18.0266 8.28649 18.5008 9.43108 18.5008 10.6246Z"
              stroke="#4E4E4E" stroke-linecap="round" stroke-linejoin="round" />
          </svg>


          {l s='Information' d='Shop.Theme.Customeraccount'}
        </span>
      </a>

      {if $customer.addresses|count}
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="addresses-link" href="{$urls.pages.addresses}">
          <span class="link-item">
            <svg width="23" height="30" viewBox="0 0 23 30" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M5.66797 12.9102H17.3346" stroke="#4E4E4E" />
              <path d="M11.5 18.7422V7.07552" stroke="#4E4E4E" />
              <path
                d="M22.4375 12.3125C22.4375 22.7279 11.5 28.7188 11.5 28.7188C11.5 28.7188 0.5625 22.7279 0.5625 12.3125C0.5625 9.41169 1.71484 6.6297 3.76602 4.57852C5.8172 2.52734 8.59919 1.375 11.5 1.375C14.4008 1.375 17.1828 2.52734 19.234 4.57852C21.2852 6.6297 22.4375 9.41169 22.4375 12.3125Z"
                stroke="#4E4E4E" stroke-linecap="round" stroke-linejoin="round" />
            </svg>

            {l s='Addresses' d='Shop.Theme.Customeraccount'}
          </span>
        </a>
      {else}
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="address-link" href="{$urls.pages.address}">
          <span class="link-item">
            <svg width="23" height="30" viewBox="0 0 23 30" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M5.66797 12.9102H17.3346" stroke="#4E4E4E" />
              <path d="M11.5 18.7422V7.07552" stroke="#4E4E4E" />
              <path
                d="M22.4375 12.3125C22.4375 22.7279 11.5 28.7188 11.5 28.7188C11.5 28.7188 0.5625 22.7279 0.5625 12.3125C0.5625 9.41169 1.71484 6.6297 3.76602 4.57852C5.8172 2.52734 8.59919 1.375 11.5 1.375C14.4008 1.375 17.1828 2.52734 19.234 4.57852C21.2852 6.6297 22.4375 9.41169 22.4375 12.3125Z"
                stroke="#4E4E4E" stroke-linecap="round" stroke-linejoin="round" />
            </svg>



            {l s='Add first address' d='Shop.Theme.Customeraccount'}
          </span>
        </a>
      {/if}

      {if !$configuration.is_catalog}
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="history-link" href="{$urls.pages.history}">
          <span class="link-item">
            <svg width="30" height="24" viewBox="0 0 30 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M1.1875 6.6875H28.8125M1.1875 7.75H28.8125M5.4375 15.1875H13.9375M5.4375 18.375H9.6875M4.375 22.625H25.625C26.4704 22.625 27.2811 22.2892 27.8789 21.6914C28.4767 21.0936 28.8125 20.2829 28.8125 19.4375V4.5625C28.8125 3.71712 28.4767 2.90637 27.8789 2.3086C27.2811 1.71083 26.4704 1.375 25.625 1.375H4.375C3.52962 1.375 2.71887 1.71083 2.1211 2.3086C1.52332 2.90637 1.1875 3.71712 1.1875 4.5625V19.4375C1.1875 20.2829 1.52332 21.0936 2.1211 21.6914C2.71887 22.2892 3.52962 22.625 4.375 22.625Z"
                stroke="#4E4E4E" stroke-linecap="round" stroke-linejoin="round" />
            </svg>



            {l s='Order history and details' d='Shop.Theme.Customeraccount'}
          </span>
        </a>
      {/if}

      {if !$configuration.is_catalog}
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="order-slips-link" href="{$urls.pages.order_slip}">
          <span class="link-item">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#clip0_1848_9537)">
                <path d="M4.5 7.5H21M4.5 12.75H21M4.5 18H21" stroke="#4E4E4E" stroke-linecap="round"
                  stroke-linejoin="round" />
                <path
                  d="M0.5 24V1L3.63043 2.21053L6.76087 1L9.8913 2.21053L13.0217 1L16.1522 2.21053L18.2391 1L21.3696 2.21053L24.5 1V24L21.3696 22.7895L18.2391 24L15.1087 22.7895L11.9783 24L8.84783 22.7895L6.23913 24L3.1087 22.7895L0.5 24Z"
                  stroke="#4E4E4E" />
              </g>
              <defs>
                <clipPath id="clip0_1848_9537">
                  <rect width="24" height="25" fill="white" transform="translate(0.5)" />
                </clipPath>
              </defs>
            </svg>



            {l s='Credit slips' d='Shop.Theme.Customeraccount'}
          </span>
        </a>
      {/if}

      {if $configuration.voucher_enabled && !$configuration.is_catalog}
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="discounts-link" href="{$urls.pages.discount}">
          <span class="link-item">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#clip0_1848_9537)">
                <path d="M4.5 7.5H21M4.5 12.75H21M4.5 18H21" stroke="#4E4E4E" stroke-linecap="round"
                  stroke-linejoin="round" />
                <path
                  d="M0.5 24V1L3.63043 2.21053L6.76087 1L9.8913 2.21053L13.0217 1L16.1522 2.21053L18.2391 1L21.3696 2.21053L24.5 1V24L21.3696 22.7895L18.2391 24L15.1087 22.7895L11.9783 24L8.84783 22.7895L6.23913 24L3.1087 22.7895L0.5 24Z"
                  stroke="#4E4E4E" />
              </g>
              <defs>
                <clipPath id="clip0_1848_9537">
                  <rect width="24" height="25" fill="white" transform="translate(0.5)" />
                </clipPath>
              </defs>
            </svg>


            {l s='Vouchers' d='Shop.Theme.Customeraccount'}
          </span>
        </a>
      {/if}

      {if $configuration.return_enabled && !$configuration.is_catalog}
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="returns-link" href="{$urls.pages.order_follow}">
          <span class="link-item">
            <svg width="27" height="19" viewBox="0 0 27 19" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M8.03125 1.84375H25.5313M8.03125 9.5H25.5313M8.03125 17.1563H25.5313M1.46875 1.84375H1.47896V1.85542H1.46875V1.84375ZM2.01563 1.84375C2.01563 1.98879 1.95801 2.12789 1.85545 2.23045C1.75289 2.33301 1.61379 2.39063 1.46875 2.39063C1.32371 2.39063 1.18461 2.33301 1.08205 2.23045C0.979492 2.12789 0.921875 1.98879 0.921875 1.84375C0.921875 1.69871 0.979492 1.55961 1.08205 1.45705C1.18461 1.35449 1.32371 1.29688 1.46875 1.29688C1.61379 1.29688 1.75289 1.35449 1.85545 1.45705C1.95801 1.55961 2.01563 1.69871 2.01563 1.84375ZM1.46875 9.5H1.47896V9.51167H1.46875V9.5ZM2.01563 9.5C2.01563 9.64504 1.95801 9.78414 1.85545 9.8867C1.75289 9.98926 1.61379 10.0469 1.46875 10.0469C1.32371 10.0469 1.18461 9.98926 1.08205 9.8867C0.979492 9.78414 0.921875 9.64504 0.921875 9.5C0.921875 9.35496 0.979492 9.21586 1.08205 9.1133C1.18461 9.01074 1.32371 8.95313 1.46875 8.95313C1.61379 8.95313 1.75289 9.01074 1.85545 9.1133C1.95801 9.21586 2.01563 9.35496 2.01563 9.5ZM1.46875 17.1563H1.47896V17.1679H1.46875V17.1563ZM2.01563 17.1563C2.01563 17.3013 1.95801 17.4404 1.85545 17.5429C1.75289 17.6455 1.61379 17.7031 1.46875 17.7031C1.32371 17.7031 1.18461 17.6455 1.08205 17.5429C0.979492 17.4404 0.921875 17.3013 0.921875 17.1563C0.921875 17.0112 0.979492 16.8721 1.08205 16.7696C1.18461 16.667 1.32371 16.6094 1.46875 16.6094C1.61379 16.6094 1.75289 16.667 1.85545 16.7696C1.95801 16.8721 2.01563 17.0112 2.01563 17.1563Z"
                stroke="#4E4E4E" stroke-linecap="round" stroke-linejoin="round" />
            </svg>


            {l s='Merchandise returns' d='Shop.Theme.Customeraccount'}
          </span>
        </a>
      {/if}

      {* {block name='display_customer_account'}
        {hook h='displayCustomerAccount'}
      {/block} *}

    </div>
  </div>
{/block}


{block name='page_footer'}
  {block name='my_account_links'}
    <div class="text-sm-center">
      <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M17.5455 12.9167V9.53125C17.5455 8.99253 17.33 8.47587 16.9464 8.09494C16.5628 7.71401 16.0425 7.5 15.5 7.5H10.0455C9.50297 7.5 8.9827 7.71401 8.5991 8.09494C8.2155 8.47587 8 8.99253 8 9.53125V21.7188C8 22.2575 8.2155 22.7741 8.5991 23.1551C8.9827 23.536 9.50297 23.75 10.0455 23.75H15.5C16.0425 23.75 16.5628 23.536 16.9464 23.1551C17.33 22.7741 17.5455 22.2575 17.5455 21.7188V18.3333M14.1364 12.9167L11.4091 15.625M11.4091 15.625L14.1364 18.3333M11.4091 15.625H23"
          stroke="#717171" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round" />
      </svg>

      <a href="{$urls.actions.logout}">
        {l s='Sign out' d='Shop.Theme.Actions'}
      </a>
    </div>
  {/block}



{/block}