<?php /* Smarty version 2.6.31, created on 2020-10-27 03:54:02
         compiled from default/admin/submit_category.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'default/admin/submit_category.html', 4, false),array('modifier', 'text', 'default/admin/submit_category.html', 28, false),array('modifier', 'attr', 'default/admin/submit_category.html', 58, false),)), $this); ?>
<!-- main navigation -->
<?php echo smarty_function_config_load(array('file' => "lang.".($this->_tpl_vars['USER_LANG'])), $this);?>


<script LANGUAGE="Javascript" SRC="modules/<?php echo $this->_tpl_vars['pcDir']; ?>
/pnincludes/AnchorPosition.js"></SCRIPT>
    <script LANGUAGE="Javascript" SRC="modules/<?php echo $this->_tpl_vars['pcDir']; ?>
/pnincludes/PopupWindow.js"></SCRIPT>
    <script LANGUAGE="Javascript" SRC="modules/<?php echo $this->_tpl_vars['pcDir']; ?>
/pnincludes/ColorPicker2.js"></SCRIPT>
    <script LANGUAGE="JavaScript">
    var cp = new ColorPicker('window');
    // Runs when a color is clicked
    function pickColor(color) {
    	document.getElementById(field).value = color;


	}

    var field;
    function pick(anchorname,target) {
    	field=target;
	    cp.show(anchorname);
	}
    </SCRIPT>

<html>
<head>

<title><?php echo ((is_array($_tmp=$this->_tpl_vars['_EDIT_PC_CONFIG_CATDETAILS'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
</title>

<link rel="stylesheet" href="<?php echo $this->_tpl_vars['css_header']; ?>
" type="text/css">

</head>
<body bgcolor="<?php echo $this->_tpl_vars['BGCOLOR2']; ?>
"/>
<?php echo $this->_tpl_vars['AdminMenu']; ?>

<form name="cats" action="<?php echo $this->_tpl_vars['action']; ?>
" method="post" enctype="application/x-www-form-urlencoded">
<!-- GATHER NEW DATA START -->
	<table border="1" cellpadding="5" cellspacing="0">
			<tr>
				<td>
					<table  width ='%100' border='1'>
						<tr>
							<td colspan ='5'>
								<table width ='%100'>
									<th align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_NEW_CAT_TITLE_S'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
</th>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table cellspacing='8' cellpadding='2'>
            						<tr>
            							<td valign="top" align="left">
            								<input type="hidden" name="newid" value=""/>
            								<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_NAME'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
            								&nbsp;<input type="text" name="newname" value="" size="20"/><br />
            								<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_TYPE'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
            								&nbsp;<select name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputCatType'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
">
               									<?php $_from = $this->_tpl_vars['cat_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['repeat']):
?>
                    								<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['value'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" >
                    									<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['name'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                    								</option>
                								<?php endforeach; endif; unset($_from); ?>
                								</select>
            							</td>
            							<td valign="top" align="left">
											<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_CONSTANT_ID'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
											&nbsp;<input type="text" name="newconstantid" value="" size="20"/><br />
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_COLOR'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
                							&nbsp;<input type="color" name="newcolor" id='newcolor' value="#FFFFFF" size="10"/>
                                                                        [<a href="javascript:void(0);" onClick="pick('pick','newcolor');return false;" NAME="pick" ID="pick"><?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_COLOR_PICK_TITLE'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
</a>]
            							</td>
            							<td valign="top" align="left">
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_DESC'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
                							&nbsp;<textarea name="newdesc" rows="3" cols="20"></textarea>
            							</td>
            							<td  valign="top" align="left">
            								<?php echo ((is_array($_tmp=$this->_tpl_vars['ALL_DAY_CAT_TITLE'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
<br />
            								&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['ALL_DAY_CAT_YES'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
<input type="radio" name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputAllDay'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueAllDay'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
"/>
            								<br />
            								&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['ALL_DAY_CAT_NO'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
<input type="radio" name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputAllDay'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueAllDayNo'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" checked />
            							</td>
            							<td  valign="top" align="left">
            								<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_DUR'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
                							&nbsp;	<?php echo ((is_array($_tmp=$this->_tpl_vars['DurationHourTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                							<input type="text" name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputDurationHour'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" value="0" size="4" />
                							<br />
                							&nbsp;	<?php echo ((is_array($_tmp=$this->_tpl_vars['DurationMinTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                							<input type="text" name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputDurationMin'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" value="0" size="4" />
                						</td>
                						<td valign="top" align="left">
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_ACTIVE'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br/>
                							<input type="radio" name="newactive" value="1"/> <?php echo ((is_array($_tmp=$this->_tpl_vars['ActiveTitleYes'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
<br/>
                							<input type="radio" name="newactive" value="0"/> <?php echo ((is_array($_tmp=$this->_tpl_vars['ActiveTitleNo'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
<br/>
                						</td>
                						<td valign="top" align="left">
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_SEQ'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br/>
                							<input type="text" name="newsequence" value="0" size="4" />
										</td>
										<td valign="top" align="left">
											<?php echo ((is_array($_tmp=$this->_tpl_vars['_ACO'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br/>
											&nbsp;<select name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputACO'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
">
               									<?php $_from = $this->_tpl_vars['ACO_List']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['acoGroupKey'] => $this->_tpl_vars['acoGroup']):
?>
													<optgroup label="<?php echo ((is_array($_tmp=$this->_tpl_vars['acoGroupKey'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" >
													<?php $_from = $this->_tpl_vars['acoGroup']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['aco']):
?>
														<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['aco']['value'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" <?php if ($this->_tpl_vars['cat']['aco'] == $this->_tpl_vars['aco']['value']): ?>selected <?php endif; ?>>
															<?php echo ((is_array($_tmp=$this->_tpl_vars['aco']['name'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

														</option>
													<?php endforeach; endif; unset($_from); ?>
													</optgroup>
                								<?php endforeach; endif; unset($_from); ?>
											</select>
										</td>

            						</tr>
            					</table>
            				</td>
            			</tr>
						<tr>
							<td>
            					<table width='%100'>
            						<tr>
                						<td colspan="4" align="left" valign="top" >
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['RepeatingHeader'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                						</td>
                					</tr>
            						<tr>
                						<td colspan="4" align="left" valign="middle" >
                							<input type="radio" name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputNoRepeat'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueNoRepeat'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" />
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['NoRepeatTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
<br />
                							<input type="radio" name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeat'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueRepeat'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
"/>
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['RepeatTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                							<input type="text" name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeatFreq'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" value="0" size="4" />

                							<select name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeatFreqType'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
">
                								<?php $_from = $this->_tpl_vars['repeat_freq_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['repeat']):
?>
                    								<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['value'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
">
                    									<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['name'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                    								</option>
                								<?php endforeach; endif; unset($_from); ?>
                							</select>
               								<br />
	                						<input type="radio" name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeatOn'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueRepeatOn'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" />
	                						 <?php echo ((is_array($_tmp=$this->_tpl_vars['RepeatOnTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
                                			<select name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeatOnNum'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
">
                                				<?php $_from = $this->_tpl_vars['repeat_on_num']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['repeat']):
?>
                    								<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['value'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
">
                    									<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['name'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                    								</option>
                    							<?php endforeach; endif; unset($_from); ?>
                							</select>
                							<select name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeatOnDay'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
">
               									<?php $_from = $this->_tpl_vars['repeat_on_day']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['repeat']):
?>
                    								<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['value'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" >
                    									<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['name'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                    								</option>
                								<?php endforeach; endif; unset($_from); ?>
                							</select>
                							&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['OfTheMonthTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
&nbsp;
                							<input type="text" name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeatOnFreq'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" value="0" size="4" />
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['MonthsTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

											<br />
											</td>
											<td >
											<!--End Date Start-->
											<table width ='%100'>
												<tr>
													<td>
													<?php echo ((is_array($_tmp=$this->_tpl_vars['NoEndDateTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                										<input type="radio" name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputEndOn'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueNoEnd'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" checked />
                										<br />
														<?php echo ((is_array($_tmp=$this->_tpl_vars['EndDateTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                										<input type="radio" name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputEndOn'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueEnd'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
"/>
                										<br />

                										<input type="text" name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputEndDateFreq'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" value="0" size="4" />

                										<select name="new<?php echo ((is_array($_tmp=$this->_tpl_vars['InputEndDateFreqType'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
">
                											<?php $_from = $this->_tpl_vars['repeat_freq_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['repeat']):
?>
                    											<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['value'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" <?php echo $this->_tpl_vars['repeat']['selected']; ?>
>
                    												<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['name'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                    											</option>
                											<?php endforeach; endif; unset($_from); ?>
                										</select>
                										<br />
                									</td>
                								</tr>

                							</table>
                					 		<!-- /End Date End -->
               							 </td>

           							 </tr>

            					</table>
            				</td>
            			</tr>
            			 <tr><td valign='bottom'><?php echo $this->_tpl_vars['FormSubmit']; ?>
</td></tr>
            		</table>
            	</td>
            </tr>
	</table>
<table border="1" cellpadding="5" cellspacing="0">
	<!--START REPEATING SECTION -->
		<?php $_from = $this->_tpl_vars['all_categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat']):
?>
			<tr>
				<td>
					<table  width ='%100' border='1'>
						<tr>
							<td colspan ='5'>
                <table width ='100%' border='0' cellpadding='0' cellspacing='0'>
                  <tr bgcolor="<?php echo $this->_tpl_vars['cat']['color']; ?>
">
                    <td align='left' width='20%'>
                      &nbsp;
                    </td>
                    <th align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_REP_CAT_TITLE_S'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
</th>
                    <td align='right' width='20%'>
                      <?php if ($this->_tpl_vars['cat']['id'] > 4 && $this->_tpl_vars['cat']['id'] != 8 && $this->_tpl_vars['cat']['id'] != 11 && $this->_tpl_vars['cat']['id'] != 6 && $this->_tpl_vars['cat']['id'] != 7): ?>
                      <!-- allow non-required categories to be deleted -->
                      <input type="checkbox" name="del[]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
"/>
                      <?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_DELETE'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                      <?php endif; ?>
                      &nbsp;
                    </td>
                  </tr>
                </table>
							</td>
						</tr>
						<tr>
							<td>
								<table cellspacing='8' cellpadding='2'>
            						<tr>
            							<td valign="top" align="left">
            								<input type="hidden" name="id[]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
"/>
            								<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_NAME'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
            								&nbsp;<input type="text" name="name[]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['name'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" size="20"/><br />
            								<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_TYPE'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
            								&nbsp;<select name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputCatType'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
[]">
               									<?php $_from = $this->_tpl_vars['cat_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['repeat']):
?>
                    								<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['value'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" <?php if ($this->_tpl_vars['cat']['value_cat_type'] == $this->_tpl_vars['repeat']['value']): ?>selected <?php endif; ?>>
                    									<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['name'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                    								</option>
                								<?php endforeach; endif; unset($_from); ?>
                								</select>
            							</td>
                                                                <?php if (( $this->_tpl_vars['globals']['translate_appt_categories'] && ( $_SESSION['language_choice'] > 1 ) )): ?>
                                                                 <td valign="top" align="left"><?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_NAME_XL'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
                                                                  <span style="color:green"><?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['nameTranslate'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
</span>
                                                                 </td>
                                                                <?php endif; ?>
            							<td valign="top" align="left">
											<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_CONSTANT_ID'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
											&nbsp;<input type="text" name="constantid[]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['constantid'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" size="20"/><br />
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_COLOR'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
                							&nbsp;<input type="color" name="color[]" id='color<?php echo '['; ?>
<?php echo $this->_tpl_vars['cat']['id']; ?>
<?php echo ']'; ?>
' value="<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['color'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" size="10"/>
                                                                        [<a href="javascript:void(0);" onClick="pick('pick','color<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
');return false;" NAME="pick" ID="pick"><?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_COLOR_PICK_TITLE'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
</a>]
            							</td>
            							<td valign="top" align="left">
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_DESC'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
                							&nbsp;<textarea name="desc[]" rows="3" cols="20"><?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['desc'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
</textarea>
            							</td>
                                                                <?php if (( $this->_tpl_vars['globals']['translate_appt_categories'] && ( $_SESSION['language_choice'] > 1 ) )): ?>
                                                                 <td valign="top" align="left"><?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_DESC_XL'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
                                                                  <span style="color:green"><?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['descTranslate'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
</span>
                                                                 </td>
                                                                <?php endif; ?>
            							<td  valign="top" align="left">
            								<?php echo ((is_array($_tmp=$this->_tpl_vars['ALL_DAY_CAT_TITLE'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
<br />
            								<?php echo ((is_array($_tmp=$this->_tpl_vars['ALL_DAY_CAT_YES'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

            								<input type="radio" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputAllDay'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueAllDay'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" <?php if ($this->_tpl_vars['cat']['end_all_day'] == 1): ?>checked<?php endif; ?>/>
            								<br />
            								&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['ALL_DAY_CAT_NO'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
<input type="radio" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputAllDay'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueAllDayNo'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" <?php if ($this->_tpl_vars['cat']['end_all_day'] == 0): ?>checked<?php endif; ?>/>
            								</td>
                						<td  valign="top" align="left">
            								<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_CAT_DUR'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
                							&nbsp;	<?php echo ((is_array($_tmp=$this->_tpl_vars['DurationHourTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                							<input type="text" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputDurationHour'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['event_durationh'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" size="4" />
                							<br />
                							&nbsp;	<?php echo ((is_array($_tmp=$this->_tpl_vars['DurationMinTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                							<input type="text" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputDurationMin'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['event_durationm'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" size="4" />
                						</td>
                						<td valign="top" align="left">
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_ACTIVE'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br/>
                							<input type="radio" name="active<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="1" data='<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['active'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
' <?php if ($this->_tpl_vars['cat']['active'] == 1): ?>checked<?php endif; ?>/>  <?php echo ((is_array($_tmp=$this->_tpl_vars['ActiveTitleYes'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
<br/>
                							<input type="radio" name="active<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="0" data='<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['active'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
' <?php if ($this->_tpl_vars['cat']['active'] == 0): ?>checked<?php endif; ?>/>  <?php echo ((is_array($_tmp=$this->_tpl_vars['ActiveTitleNo'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
<br/>
                						</td>
                						<td valign="top" align="left">
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['_PC_SEQ'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br/>
                							<input type="text" name="sequence[]" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['sequence'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" size="4" />
                						</td>
										<td valign="top" align="left">
											<?php echo ((is_array($_tmp=$this->_tpl_vars['_ACO'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br/>
											&nbsp;<select name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputACO'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
[]">
               									<?php $_from = $this->_tpl_vars['ACO_List']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['acoGroupKey'] => $this->_tpl_vars['acoGroup']):
?>
													<optgroup label="<?php echo ((is_array($_tmp=$this->_tpl_vars['acoGroupKey'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" >
													<?php $_from = $this->_tpl_vars['acoGroup']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['aco']):
?>
														<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['aco']['value'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" <?php if ($this->_tpl_vars['cat']['aco'] == $this->_tpl_vars['aco']['value']): ?>selected <?php endif; ?>>
															<?php echo ((is_array($_tmp=$this->_tpl_vars['aco']['name'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

														</option>
													<?php endforeach; endif; unset($_from); ?>
													</optgroup>
                								<?php endforeach; endif; unset($_from); ?>
											</select>
										</td>
            						</tr>
            					</table>
            				</td>
            			</tr>
						<tr>
							<td>
            					<table width='%100'>
            						<tr>
                						<td colspan="4" align="left" valign="top" >
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['RepeatingHeader'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                						</td>
                					</tr>
            						<tr>
                						<td colspan="4" align="left" valign="middle" >
                							<input type="radio" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputNoRepeat'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueNoRepeat'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" <?php if ($this->_tpl_vars['cat']['event_repeat'] == 0): ?>checked<?php endif; ?>/>
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['NoRepeatTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
<br />
                							<input type="radio" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeat'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueRepeat'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" <?php if ($this->_tpl_vars['cat']['event_repeat'] == 1): ?>checked<?php endif; ?> />
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['RepeatTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                							<input type="text" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeatFreq'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['event_repeat_freq'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" size="4" />

                							<select name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeatFreqType'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
">
                								<?php $_from = $this->_tpl_vars['repeat_freq_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['repeat']):
?>
                    								<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['value'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" <?php if ($this->_tpl_vars['cat']['event_repeat_freq_type'] == $this->_tpl_vars['repeat']['value']): ?>selected<?php endif; ?>>
                    									<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['name'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                    								</option>
                								<?php endforeach; endif; unset($_from); ?>
                							</select>
               								<br />
	                						<input type="radio" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeatOn'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueRepeatOn'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
"<?php if ($this->_tpl_vars['cat']['event_repeat'] == 2): ?>checked<?php endif; ?> />
	                						 <?php echo ((is_array($_tmp=$this->_tpl_vars['RepeatOnTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
:<br />
                                			<select name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeatOnNum'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
">
                                				<?php $_from = $this->_tpl_vars['repeat_on_num']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['repeat']):
?>
                    								<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['value'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" <?php if ($this->_tpl_vars['cat']['event_repeat_on_num'] == $this->_tpl_vars['repeat']['value']): ?>selected<?php endif; ?>>
                    									<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['name'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                    								</option>
                    							<?php endforeach; endif; unset($_from); ?>
                							</select>
                							<select name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeatOnDay'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
">
               									<?php $_from = $this->_tpl_vars['repeat_on_day']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['repeat']):
?>
                    								<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['value'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" <?php if ($this->_tpl_vars['cat']['event_repeat_on_day'] == $this->_tpl_vars['repeat']['value']): ?>selected <?php endif; ?>>
                    									<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['name'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                    								</option>
                								<?php endforeach; endif; unset($_from); ?>
                							</select>
                							&nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['OfTheMonthTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>
&nbsp;
                							<input type="text" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputRepeatOnFreq'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['event_repeat_on_freq'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" size="4" />
                							<?php echo ((is_array($_tmp=$this->_tpl_vars['MonthsTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

											<br />
											</td>
											<td >
											<!--End Date Start-->
											<table width ='%100'>
												<tr>
													<td>
														<?php echo ((is_array($_tmp=$this->_tpl_vars['NoEndDateTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                										<input type="radio" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputEndOn'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueNoEnd'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
"  <?php if ($this->_tpl_vars['cat']['end_date_flag'] == 0): ?> checked<?php endif; ?> />
                										<br />
														<?php echo ((is_array($_tmp=$this->_tpl_vars['EndDateTitle'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                										<input type="radio" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputEndOn'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['ValueEnd'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
"  <?php if ($this->_tpl_vars['cat']['end_date_flag'] == 1): ?> checked<?php endif; ?> />
                										<br />

                										<input type="text" name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputEndDateFreq'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['end_date_freq'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" size="4" />
                										<select name="<?php echo ((is_array($_tmp=$this->_tpl_vars['InputEndDateFreqType'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo '['; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['cat']['id'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
<?php echo ']'; ?>
">
                											<?php $_from = $this->_tpl_vars['repeat_freq_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['repeat']):
?>
                    											<option value="<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['value'])) ? $this->_run_mod_handler('attr', true, $_tmp) : attr($_tmp)); ?>
" <?php if ($this->_tpl_vars['cat']['end_date_type'] == $this->_tpl_vars['repeat']['value']): ?>selected <?php endif; ?>>
                    												<?php echo ((is_array($_tmp=$this->_tpl_vars['repeat']['name'])) ? $this->_run_mod_handler('text', true, $_tmp) : text($_tmp)); ?>

                    											</option>
                											<?php endforeach; endif; unset($_from); ?>
                										</select>
                									</td>
                								</tr>

                							</table>
                					 		<!-- /End Date End -->
               							 </td>
           							 </tr>
           							             					</table>
            				</td>
            			</tr>
            			<tr><td valign='bottom'><?php echo $this->_tpl_vars['FormSubmit']; ?>
</td></tr>
            		</table>
            	</td>
            </tr>
 		<!-- /REPEATING ROWS -->
		<?php endforeach; endif; unset($_from); ?>
	</table>

<input type="hidden" name="pc_html_or_text" value="text" selected>

<?php echo $this->_tpl_vars['FormHidden']; ?>


<?php echo $this->_tpl_vars['FormSubmit']; ?>

</form>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['TPL_NAME'])."/views/footer.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>