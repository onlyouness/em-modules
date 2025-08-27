 <div class="atouts atouts-bg">
     <div class="atouts-tro container">
         <h2>
             {l s='Our Benefits' mod='nosatouts'}
             <hr>
         </h2>
         <div class="atouts-three ">
             {foreach from=$atoutbanner item=banner}
                 <div class="atouts-item">
                     <img src="/modules/nosatouts/img/{$banner.image}">
                     <div>
                         <h3>{$banner.title}</h3>
                         <p>{$banner.description}</p>
                     </div>
                 </div>

             {/foreach}
         </div>
     </div>
</div>