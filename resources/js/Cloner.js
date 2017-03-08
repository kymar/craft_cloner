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
				matchSection = Craft.path.match(/^settings\/sections\/([0-9]+)\/entrytypes$/),
				rowId = $row.data('id');

			if(matchSection)
			{
				var sectionId = matchSection[1];

				var cloneButton = $('<td class="thin"><a class="add icon" title="Clone" role="button"></a></td>').on('click', function (e)
				{
					e.preventDefault();

					var newEntryName;
					if(newEntryName = prompt('New Entry Type Name'))
					{
						var data = {
							sectionId: sectionId,
							oldEntryId: rowId,
							newEntryName: newEntryName,
							newEntryHandle: generateHandle(newEntryName)
						};

						Craft.postActionRequest('cloner/clone', data, function (response)
						{

						});
					}

					return false;
				});

				$row.find('[data-title="Handle"]').after(cloneButton);
			}

		})
	}
});