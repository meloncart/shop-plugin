function showOMRecordModal(elem, handler, id)
{
	$(elem).popup({
		handler: handler,
		extraData: {
			id: id
		}
	});
}
