$(function()
{
	//	https://github.com/pixelandtonic/Craft-Release/blob/46ec3f0496b13c34c91f488ff5384e210b726ea8/app/resources/js/craft/HandleGenerator.js
	function generateHandle(sourceVal)
	{
		// Remove HTML tags
		var handle = sourceVal.replace("/<(.*?)>/g", '');

		// Remove inner-word punctuation
		handle = handle.replace(/['"‘’“”\[\]\(\)\{\}:]/g, '');

		// Make it lowercase
		handle = handle.toLowerCase();

		// Convert extended ASCII characters to basic ASCII
		handle = Craft.asciiString(handle);

		// Handle must start with a letter
		handle = handle.replace(/^[^a-z]+/, '');

		// Get the "words"
		var words = Craft.filterArray(handle.split(/[^a-z0-9]+/)),
			handle = '';

		// Make it camelCase
		for (var i = 0; i < words.length; i++)
		{
			if (i == 0)
			{
				handle += words[i];
			}
			else
			{
				handle += words[i].charAt(0).toUpperCase()+words[i].substr(1);
			}
		}

		return handle;
	}

	var entryTypes = $('#entrytypes');

	if(entryTypes.length)
	{
		entryTypes.find('tr').each(function ()
		{
			var $row = $(this),
				rowId = $row.data('id');

			var cloneButton = $('<td class="thin"><a class="add icon" title="Clone" role="button"></a></td>').on('click', function (e)
			{
				e.preventDefault();

				var newEntryName;
				if(newEntryName = prompt('New Entry Type Name'))
				{
					var data = {
						oldEntryId: rowId,
						newEntryName: newEntryName,
						newEntryHandle: generateHandle(newEntryName)
					};

					Craft.postActionRequest('cloner/clone', data, function (response)
					{
						if(typeof response.success != 'undefined')
						{
							window.location.reload();
						}
					});
				}

				return false;
			});

			$row.find('[data-title="Handle"]').after(cloneButton);
		});
	}

	var sections = $('#sections');

	if(sections.length)
	{
		sections.find('tr').each(function ()
		{
			var $row = $(this),
				rowId = $row.data('id');

			var cloneButton = $('<td class="thin"><a class="add icon" title="Clone" role="button"></a></td>').on('click', function (e)
			{
				e.preventDefault();

				var newSectionName;
				if(newSectionName = prompt('New Section Name'))
				{
					var data = {
						oldSectionId: rowId,
						newSectionName: newSectionName,
						newSectionHandle: generateHandle(newSectionName)
					};

					Craft.postActionRequest('cloner/cloneSection', data, function (response)
					{
						if(typeof response.success != 'undefined')
						{
							window.location.reload();
						}
					});
				}

				return false;
			});

			$row.find('[data-title="Entry Types"]').after(cloneButton);
		});
	}

});