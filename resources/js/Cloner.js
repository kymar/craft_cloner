$(function()
{
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
							newEntryName: newEntryName
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