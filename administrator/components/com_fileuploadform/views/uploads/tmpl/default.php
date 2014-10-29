<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<form action="<?= JRoute::_($this->link) ?>" method="post" target="_self" id="adminForm" name="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?= ''//$this->sidebarRender() ?>
		<?= ''//$this->filterRender() ?>
	</div>
	<div id="j-main-container" class="span10">
		<?= ''//$this->headerRender(false) ?>
		<?php if (!empty($this->uploads)) { ?>
			<div class="accordion" id="membersaccordion">
				<?php foreach ($this->members as $key => $member) { ?>
					<div class="accordion-group">
						<div class="accordion-heading">
							 <a class="accordion-toggle" data-toggle="collapse" data-parent="#membersaccordion" href="#collapse<?= $member->m_id ?>">
								<?= $this->escape($member->name) ?> (<?= $this->escape($member->email) ?>)
								<span class="pull-right label label-<?= $member->m_duespaid==0?'warning':($member->m_duespaid<date('Y')?'important':'success') ?>"><?= $member->m_duespaid==0?'Dues Status Unknown':($member->m_duespaid<date('Y')?'Dues Not Current':'Dues Current') ?></span>
							</a>
						</div>
						<div id="collapse<?= $member->m_id ?>" class="accordion-body collapse">
							<div class="accordion-inner">
								<div class="row-fluid">
									<div class="span6 form-horizontal">
										<div class="control-group">
											<label class="control-label">Name</label>
											<div class="controls">
												<input type="text" class="input-large" name="users[<?= $member->u_id ?>][name]" value="<?= $this->escape($member->name) ?>" placeholder="Name">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Address</label>
											<div class="controls">
												<input type="text" class="input-large" name="members[<?= $member->u_id ?>][m_address]" value="<?= $this->escape($member->m_address) ?>" placeholder="Address">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">City</label>
											<div class="controls">
												<input type="text" class="input-large" name="members[<?= $member->u_id ?>][m_city]" value="<?= $this->escape($member->m_city) ?>"  placeholder="City">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">State</label>
											<div class="controls">
												<select class="input-large m_state" name="members[<?= $member->u_id ?>][m_state]" placeholder="State">
													<option value=""></option>
													<?php foreach ($this->states as $code => $state) { ?>
														<option value="<?= $this->escape($code) ?>" <?= $code==$member->m_state?'selected':'' ?>>
															<?= $this->escape($state) ?>
														</option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Zip</label>
											<div class="controls">
												<input type="text" class="input-large" name="members[<?= $member->u_id ?>][m_zip]" value="<?= $this->escape($member->m_zip) ?>" placeholder="Zip">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Phone</label>
											<div class="controls">
												<input type="text" class="input-large" name="members[<?= $member->u_id ?>][m_phone]" value="<?= $this->escape($member->m_phone) ?>" placeholder="Phone">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Email</label>
											<div class="controls">
												<input type="text" class="input-large" name="users[<?= $member->u_id ?>][email]" value="<?= $this->escape($member->email) ?>" placeholder="Email">
											</div>
										</div>
									</div>
									<div class="span6 form-horizontal">
										<div class="control-group">
											<label class="control-label">Birthday</label>
											<div class="controls">
												<select class="input-medium m_bmonth" name="members[<?= $member->u_id ?>][m_bmonth]"  placeholder="Month">
													<option value=""></option>
													<?php for ($i = 1; $i <= 12; $i++) { ?>
														<option value="<?= $i ?>" <?= $member->m_bmonth==$i?'selected':'' ?>>
															<?= HBHtml::dateOut(null,'@'.mktime(0, 0, 0, $i+1, 0, 0),"F") ?>
														</option>
													<?php } ?>
												</select>
												<input type="text" class="input-mini" name="members[<?= $member->u_id ?>][m_bday]" value="<?= $member->m_bday?$this->escape($member->m_bday):'' ?>"  placeholder="Day">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Anniversary</label>
											<div class="controls">
												<select class="input-medium m_amonth" name="members[<?= $member->u_id ?>][m_amonth]" placeholder="Month">
													<option value=""></option>
													<?php for ($i = 1; $i <= 12; $i++) { ?>
														<option value="<?= $i ?>" <?= $member->m_amonth==$i?'selected':'' ?>>
															<?= HBHtml::dateOut(null,'@'.mktime(0, 0, 0, $i+1, 0, 0),"F") ?>
														</option>
													<?php } ?>
												</select>
												<input type="text" class="input-mini" name="members[<?= $member->u_id ?>][m_aday]" value="<?= $member->m_aday>0?$this->escape($member->m_aday):'' ?>"  placeholder="Day">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Member Since</label>
											<div class="controls">
												<?= HBHtml::dateOut('date',$member->registerDate) ?>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Dues Paid Through</label>
											<div class="controls">
												<select class="input-medium m_duespaid" name="members[<?= $member->u_id ?>][m_duespaid]" placeholder="Dues Paid Through">
													<?php $blank = !(int)$member->m_duespaid;
													$found = $blank ? true : $found; ?>
													<option value="" <?= $blank?'selected':'' ?>></option>
													<?php for ($i = -10+date('Y'); $i <= 25+date('Y'); $i++) {
														$found = $i==$member->m_duespaid ? true : $found; ?>
														<option value="<?= $i ?>" <?= $i==$member->m_duespaid?'selected':'' ?>><?= $i ?></option>
													<?php }
													if (!$found) { ?>
														<option value="<?= $member->m_duespaid ?>" selected><?= $member->m_duespaid ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Username</label>
											<div class="controls">
												<input type="text" value="<?= $this->escape($member->username) ?>" class="input-large" name="users[<?= $member->u_id ?>][username]" placeholder="Username">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Password Reset</label>
											<div class="controls">
												<input type="password" value="" class="input-large" name="users[<?= $member->u_id ?>][password]" placeholder="Password" autocomplete="off">
											</div>
										</div>
										<div class="control-group">
											<label class="control-label">Confirm Password</label>
											<div class="controls">
												<input type="password" value="" class="input-large" name="users[<?= $member->u_id ?>][password2]" placeholder="Confirm Password" autocomplete="off">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php  } ?>
			</div>
			<?= $this->pagination->getListFooter() ?>
		<?php } else { ?>
<!--
			<p><strong>-- No Matching Members --</strong></p>
-->
		<?php } ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?= ''//$this->escape($this->state->get('list.ordering')) ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?= ''//$this->escape($this->state->get('list.direction')) ?>" />
	<input type="hidden" name="option" value="com_honeybee" />
	<input type="hidden" name="view" value="<?= $this->escape($this->view) ?>" />
	<input type="hidden" name="layout" value="<?= $this->escape($this->layout) ?>" />
	<?=  JHtml::_('form.token') ?>
</form>
<script type="text/javascript">
	jQuery(function($){
		//~ $('#sortTable, #directionTable').select2().change(function(){
			//~ Joomla.tableOrdering($('#sort-box').val(),jQuery('#dir-box').val(),'',jQuery(this).closest('form')[0]);
			//~ return false;
		//~ });
		//~ $('.m_state, .m_bmonth, .m_amonth, .m_duespaid').select2({
			//~ allowClear: true,
		//~ });
		//~ $('#limit').select2().click(function(){
			//~ $(this).closest('form').submit();
		//~ });
	});
</script>
