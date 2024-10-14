	<div>
		<label class="labelarchivos" for="upload">
			<input type="file" id="upload" name='upload[]' accept=".doc,.docx,.xls,.xlsx,.pppt,.pptx,.pdf,image/*,video/*,.mp3,audio/wav,.txt" required	 multiple>
			Seleccionar Archivos
		</label>
	</div>
	<div class="files">
		<h3>Archivos Seleccionados</h3>
		<ul id='larchivos' class="larchivos"></ul>
		<input type="hidden" name="archivos" id='archivos' value="">
	</div>
	<input type="submit" value="Subir" name="enviararchivos" id="enviararchivos" />
	{{-- <a href="{{ url('/subir-archivos-requerimiento/'.$idopcion.'/'.$idregistro) }}"> --}}
		<input type="button" value="Cancelar" name="btncancelar" id="btncancelar" />
	{{-- </a> --}}




