<div class="lazyload item item-{{INDEX}}" id="{{MAILLOTID}}" data-item="{&quot;id&quot;:&quot;{{MAILLOTID}}&quot;,&quot;src&quot;:&quot;{{IMGMAILLOTFACE}}&quot;}">
	<a class="item-link" href="{{URL}}">
		<div class="picture">
			<img   data-src="{{IMGMAILLOTFACE}}" class="item-pict item-pict-face lazyload"  alt="Vue face maillot {{MAILLOTID}}" />
			<img   data-src="{{IMGMAILLOTDOS}}"  class="item-pict item-pict-dos lazyload "   alt="Vue dos maillot {{MAILLOTID}}" />
			<img   data-src="{{IMGLOGO}}"        class="item-pict-logo lazyload"            alt="Logo" />
			
			
		</div>
	</a>
	<div class="item-like">
			<a class="get_data loadpopin" data-popin="popin-vote" href="javascript:;">
				<span class="icon-vote"></span>
				<span class="nblike count-votes">{{NBVOTES}} votes</span>
			</a>
			
			<a class="get_data loadpopin item-like-option" data-popin="popin-share" href="javascript:;"><span class="icon-option"></span></a>	
	</div>
	<div class="item-nom-ville">
		<div class="item-name">{{NOMCLUB}}</div>
		<div class="item-city">{{VILLE}}</div>
	</div>
	<div class="button-search-vote ">
		<a class="get_data loadpopin button-hover-vote button-vote" data-popin="popin-vote" href="javascript:;">voter</a>
	</div>
</div>