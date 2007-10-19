/**
 * @description		prototype.js based context menu
 * @author			Juriy Zaytsev; kangax@gmail.com; http://thinkweb2.com/projects/prototype
 * @version			0.5
 * @date			8/22/07
 * @requires		prototype.js 1.6.0_rc0
*/

// temporary unobtrusive workaround for 'contextmenu' event missing from DOMEvents in 1.6.0_RC0
/*
if (!Event.DOMEvents.include('contextmenu')) {
	Event.DOMEvents.push('contextmenu')
}
*/
// nifty helper for setting element's property in a chain-friendly manner
Element.addMethods({
	__extend: function(element, hash) {
		return Object.extend($(element), hash);
	}
})

//if (Object.isUndefined(Proto))
	{ var Proto = { } }

document.viewport = {
  getDimensions: function() {
    var dimensions = { };
    $w('width height').each(function(d) {
      var D = d.capitalize();
      dimensions[d] = self['inner' + D] ||
       (document.documentElement['client' + D] || document.body['client' + D]);
    });
    return dimensions;
  }
 }


Proto.Menu = Class.create();
Proto.Menu.prototype = {
	initialize: function (options) {
		this.options = Object.extend({
			selector: '.contextmenu',
			className: '.protoMenu',
			pageOffset: 5,
			fade: false
		}, options || { });
		// Setting fade to true only if Effect is defined
		this.options.fade = this.options.fade && !Object.isUndefined(Effect);
		var opts = {className: this.options.className, style: 'display: none'};
		//this.container = new Element('div', opts);
		this.container = $(document.createElement('div'));
		this.container.className=this.options.className;
		this.container.style.display='none';
		this.container.style.zIndex='100';
		this.options.menuItems.each(this.addElement.bind(this));
		var bbody = document.getElementsByTagName('body').item(0);
		bbody.appendChild(this.container);

		Event.observe(document, 'click', function(e){
			this.container.hide();
		}.bind(this));

		$$(this.options.selector).invoke('observe', 'contextmenu', function(e){
			this.show(e);
		}.bind(this));

		this.containerWidth = this.container.getWidth();
		this.containerHeight = this.container.getHeight();
	},
	addElement:function(item){
		if (item.separator){
				var el=document.createElement('div');
				el.className='separator';

			} else {
				var el=document.createElement('a');
				el.href= '#';
				// begin scott change
				//el.title=item.name;
				if (item.title){
					el.title=item.title;
				}
				el.className=(item.className ? item.className : '') + (item.disabled ? ' disabled' : '');
				//el.className=item.disabled ? ' disabled' : '';
				Event.observe(el, 'click', this.onClick.bind(this));
				el.update(item.name);
				el._callback= item.callback;
			}
			this.container.appendChild(el);
	},
	removeElement:function(item){
		this.options.menuItems.without(function(e){return e.title = item.title;});
	},
	show: function(e) {
		Event.stop(e);
	    this.lastTarget=Event.element(e);
		var viewport = document.viewport.getDimensions();
		this.container.setStyle({
			left: ((Event.pointerX(e) + this.containerWidth) > viewport.width ? (viewport.width - this.containerWidth - this.options.pageOffset) : Event.pointerX(e)) + 'px',
			top: ((Event.pointerY(e) + this.containerHeight) > viewport.height && Event.pointerY(e) > this.containerHeight ? (Event.pointerY(e) - this.containerHeight - this.options.pageOffset) : Event.pointerY(e)) + 'px'
		}).hide();
		this.options.fade ? Effect.Appear(this.container, {duration: 0.25}) : this.container.show();
		return false;
	},
	onClick: function(e) {
		Event.stop(e);
		if (Event.element(e)._callback && !Event.element(e).hasClassName('disabled')) {
			this.container.hide();
			Event.element(e)._callback(this.lastTarget.up(this.options.selector));
		}
		return false;
	}
}