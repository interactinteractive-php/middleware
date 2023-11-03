<div data-structureid="<?php echo $this->metaDataId; ?>" data-commentstructureid="<?php echo $this->commentStructureId; ?>" data-recordid="<?php echo $this->metaValueId; ?>" data-comment-uniqid="<?php echo $this->uniqId; ?>">
   
    <div class="chat-form p-0 dialog-chat mt0">
        <div class="media flex-column flex-sm-row mt-2 mb-2 chat-addcontrol">
            <div class="mr-md-1 mb-2 mb-md-0">
                <?php echo Ue::getSessionPhoto('class="rounded-circle avatar" width="36" height="36"'); ?>
            </div>
            <div class="media-body">
                <?php 
                echo Form::hidden(array('name' => 'metaDataId', 'value' => $this->metaDataId));
                echo Form::hidden(array('name' => 'metaValueId', 'value' => $this->metaValueId));
                echo Form::textArea(
                    array(
                        'name' => 'mdcomment_text',
                        'id' => 'mdcomment_text', 
                        'class' => 'form-control p-1 bpaddon-mention-autocomplete mention',
                        'placeholder' => $this->lang->line('task_comment_write'),
                        'onkeypress' => 'if(event.keyCode == 13) saveMdCommentProcessValue_'.$this->uniqId.'(this);'
                    )
                ); 
                ?>
            </div>
            <div class="pl8 pt15">
                <a href="javascript:;" onclick="saveBtnMdCommentProcessValue_<?php echo $this->uniqId; ?>(this);"><i class="fas fa-paper-plane font-size-15"></i></a>
            </div>
        </div>
    </div>
    
    <div class="scrollerFalse" data-scroll-panel="1">    
        <?php echo $this->commentRows; ?>
    </div>    
</div>